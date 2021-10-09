<?php

// エラーログの設定
ini_set('log_errors','On');
ini_set('error_log','php.log');

// デバッグフラグ
$debug_flg = true;

// エラーメッセージ格納用の配列
$err_msg = array();

// エラーメッセージ定数
define('MSG01','入力必須です');
define('MSG02','文字以上で入力してください');
define('MSG03','文字以内で入力してください');
define('MSG04','Emialの形式で入力してください');
define('MSG05','半角英数字で入力してください');
define('MSG06','パスワード（再入力）が一致していません');
define('MSG07','このメールアドレスはすでに登録されています');
define('MSG08','メールアドレスもしくはパスワードが一致しません');
define('MSG09','古いパスワードが一致していません');
define('MSG10','ネットワークエラーが発生しました。しばらく時間をおいてから、もう一度試してください');
define('MSG11','新しいパスワードが古いパスワードと同じです');
define('MSG12','再発行キーが違います');
define('MSG13','再発行キーの有効期限が切れています');
define('MSG14','半角数字で入力してください');
define('MSG15','新パスワードは旧パスワードと異なるものを入力してください');


// デバッグ関数
function debug($str){
    global $debug_flg;
    if($debug_flg){
        error_log('デバッグ：'.$str);
    }
}

function debugLogStart(){
    debug('デバッグログ関数です');
}



// セッションを使う準備をする
session_save_path('./var/tmp');

ini_set('session.gc_maclifetime',60*60*24*30);

ini_set('session.cookie_lifetime',60*60*24*30);

session_start();

session_regenerate_id();





// バリデーション関数
// 未入力チェック
function validRequire($str,$key){
  global $err_msg;
  if($str === ''){
    $err_msg[$key] = MSG01;
  }
}

// 最小文字数チェック
function validMinLen($str,$key,$min = 6){
  global $err_msg;
  if(mb_strlen($str)<$min){
    $err_msg[$key] =$min.MSG02;
  }
}

// 最大文字数チェック
function validMaxLen($str,$key,$max=256){
  global $err_msg;
  if(mb_strlen($str)>$max){
    $err_msg[$key] = $max.MSG03;
  }
}

// 半角英数字チェック
function validHalf($str,$key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    $err_msg[$key] = MSG05;
    
  }
}

// 半角数字チェック
function validNumber($str,$key){
  global $err_msg;
  if(!preg_match("/^[0-9]+$/",$str)){
    $err_msg[$key] = MSG14;
  }
}

// 二つの値が一致しているかチェック
function validMatch($str1,$str2,$key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG06;
  }
}

// プロフィール編集画面において、DBデータとPOSTデータの比較関数



// メールアドレス形式チェック
function validEmail($str,$key){
  global $err_msg;
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/",$str)){
    $err_msg[$key] =MSG04;
  }
}

// パスワード形式まとめチェック
function validPassAll($str,$key){
  global $err_msg;
  validMinLen($str,$key);
  if(!empty($err_msg)){
    return;
  };
  validMaxLen($str,$key);
  if(!empty($err_msg)){
    return;
  };
  validHalf($str,$key);
  if(!empty($err_msg)){
    return;
  };
}



// メールアドレス重複チェック
function validEmailDup($email){
  global $err_msg;
  debug('メールアドレス重複チェック関数です');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT id FROM users WHERE email=:email AND delete_flg=0';
    $data = array(':email'=>$email);

    // クエリの実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('重複チェックに成功しました');
      $result = $stmt->rowCount();
      if($result){
        debug('メールアドレスが重複しています');
        $err_msg['email'] = MSG07;
      }else{
        debug('メールアドレスは重複していません');
      }
    }
  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

// 旧パスワードと新パスワードが異なることを判定する関数
function validDiff($str1,$str2,$key){
  global $err_msg;
  if($str1 == $str2){
    $err_msg[$key] = MSG15;
  }
}



// メールアドレスとパスワードから、適正ユーザーか判定する関数
function getUserIdAndPass($email,$pass){
  debug('メールアドレスとパスワードから、DBに登録されているか判定します');
  global $err_msg;
  try{
    $dbh = dbConnect();
    $sql = 'SELECT password,id FROM users WHERE email=:email
    AND delete_flg=0';
    $data = array(':email'=>$email);
    // クエリの実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('判定に成功しました');
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(!empty($result) && password_verify($pass,$result['password'])){
        debug('パスワードは一致しています');
        return $result;
      }else{
        debug('パスワードは一致していません');
        $err_msg['email'] = MSG08;
        return false;
      }
    }
  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

// user_idからユーザー情報を取得する関数
function getUserFromId($u_id){
  debug('user_idからユーザー情報を取得します');
  try{
    $dbh = dbConnect();
    $sql ='SELECT email,password,name,age,job,pic FROM users
    WHERE id=:id AND delete_flg=0';
    $data = array(':id'=>$u_id);

    // クエリの実行
    $stmt = queryPost($dbh,$sql,$data);
    if($stmt){
      debug('SELECT文の実行に成功しました');
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result;
    }else{
      debug('SELECT文の実行に成功しました');
    }
  } catch (Exception $e){
    error_log('エラーが発生：'.$e->getMessage());
  }

}




// メールアドレスからユーザーIDを取得
function getUserIdFromEmail($email){
  global $err_msg;
  debug('メールアドレスからユーザーIDを取得する関数です');
  try{
    $dbh = dbConnect();
    $sql = 'SELECT id FROM users WHERE email=:email AND delete_flg=0';
    $data = array(':email'=>$email);

    // クエリの実行
    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('重複チェックに成功しました');
      $result = $stmt->rowCount();
      if($result){
        debug('メールアドレスが重複しています');
        $err_msg['email'] = MSG07;
      }else{
        debug('メールアドレスは重複していません');
      }
    }
  } catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}

// DB接続関数
function dbConnect(){
  $dsn = 'mysql:host=localhost;dbname=rakusyu;charset=utf8';
  $user = 'root';
  $password = '';
  $options = array(
    // エラーモードの設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // フェッチモードを連想配列形式に
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // row->count()を使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}

// クエリ実行関数
function queryPost($dbh,$sql,$data){
  $stmt = $dbh->prepare($sql);

  if($flg =$stmt->execute($data)){
    debug('SQL文の実行に成功しました');
    // debug('$flgの中身：'.print_r($flg,true));
    return $stmt;
  }else{
    debug('SQL文の実行に失敗しました');
    debug('失敗したSQL文'.$sql);
    return false;
  }
}

// メール送信関数
function sendMail($to,$subject,$comment,$from){
  debug('メール送信関数です');
  if(!empty($to) && !empty($subject) && !empty($comment)){
  // メール送信の設定
  mb_language('Japanese');
  mb_internal_encoding('UTF-8');

    $restlt = mb_send_mail($to,$subject,$comment,'From:'.$from);
    if($restlt){
      debug('メールを送信しました');
      return true;
    }else{
      debug('エラーが発生：メールの送信に失敗しました');
      return false;
    }
  }
}

// ランダムキー生成関数
function makeRandomKey($len=8){
  $key = '';
  $char = 'abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789';
  for ($i=1;$i<=$len;$i++){
    $key.= $char[mt_rand(0,mb_strlen($char)-1)];
  } 
  return $key;

}

// エラーClass付与関数
function getErr($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return 'err';
  }
}

// ユーザー情報を取得する
function getUser($u_id){
  debug('ユーザー情報を取得します');
  try{
    $dbh = dbConnect();
    $sql ='SELECT * FROM users WHERE id=:u_id AND delete_flg =0';
    $data = array(':u_id'=>$u_id);

    // クエリの実行
    $stmt = queryPost($dbh,$sql,$data);
    
    if($stmt){
      debug('ユーザー情報の取得に成功しました');
      $restlt = $stmt->fetch(PDO::FETCH_ASSOC);
      return $restlt;
    }else{
      debug('ユーザー情報の取得に失敗しました');
      return false;
    }
  }catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG10;
  }
}



// フォーム入力維持機能
function getFormData($str){
  debug($str.'のフォーム入力維持判定です');
  global $err_msg;
  global $dbUserData;
  if(!empty($dbUserData[$str])){
    if(empty($err_msg[$str])){
      if(isset($_POST[$str]) && $_POST[$str] !== $dbUserData[$str]){
          debug('ループ１');
          return $_POST[$str];
      }else{
        debug('ループ２');
        return $dbUserData[$str];
      }
      }else{
        debug('ループ３');
        return $_POST[$str];
      }
  } else {
    if(isset($_POST[$str])){
      debug('ループ４');
      return $_POST[$str];
    }   
  }
}

// 画像アップロード関数
// tmpファイルをuser_imgフォルダに格納し、そのパスを返す
function uploadImg($file, $key){
  debug('画像アップロード開始');
  debug('FILE情報：'.print_r($file,true));
  // まず、アップロードされたファイルのバリデーションチェック
  // 1.$_FILEのerrorの確認
  // 2.MINEタイプの確認
  // 3.移動先のファイルの名前の生成
  // 4.移動元から移動先にファイルの移動
  // 5.移動先のフォルダを返す

  if(isset($file['name']) && is_int($file['error'])){
    try{

      switch($file['error']){
        case UPLOAD_ERR_OK:
          break;
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('ファイルサイズが大きすぎます');
          case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('ファイルが選択されていません');
          default:
            throw new RuntimeException('その他のエラーが発生しました');
      }
      $type = @exif_imagetype($file['tmp_name']);
      debug('$typeの中身：'.print_r($type,true));
      if(!in_array($type, [IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
        throw new RuntimeException('画像形式が未対応です');
      }
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

      if(!move_uploaded_file($file['tmp_name'],$path)){
          throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }

      chmod($path,0644);

      debug('ファイルは正常にアップロードされました');
      debug('ファイルパス'.$path);
      return $path;

    } catch (Exception $e){
      global $err_msg;
      error_log('エラーが発生'.$e->getMessage());
      $err_msg['pic'] = $e->getMessage();
    }
  }

};











?>