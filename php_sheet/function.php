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
define('MSG04','Emailの形式で入力してください');
define('MSG05','半角英数字で入力してください');
define('MSG06','パスワード（再入力）が一致していません');
define('MSG07','ネットワークエラーが発生しました。時間をおいてお試しください');
define('MSG08','メールアドレスまたはパスワードが間違っています');
define('MSG09','古いパスワードが違います');
define('MSG10','このメールアドレスは登録されていません');
define('MSG11','再発行キーが違います');
define('MSG12','再発行キーの有効期限が切れています');
define('MSG13','半角数値で入力してください');
define('MSG14','このメールアドレスはすでに登録されています');


define('SUC01','パスワードの変更が完了しました');
define('SUC02','再発行キーを送信しました');
define('SUC03','新規パスワードを送信しました');
define('SUC04','新しい習慣を登録しました');
define('SUC05','習慣の編集に成功しました');
define('SUC06','プロフィールの編集に成功しました');
define('SUC07','記録の更新に成功しました。お疲れ様です!!');

// デバッグ関数
function debug($str){
    global $debug_flg;
    if($debug_flg){
        error_log('デバッグ：'.$str);
    }
}

// デバッグログ関数
function debugLogStart(){
    global $debug_flg;
    if($debug_flg){
        debug('---------デバッグログ関数----------');
        debug('$_SESSIONの中身：'.print_r($_SESSION,true));
    }
}


// セッションの準備
session_save_path('var/tmp');
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);

session_start();
session_regenerate_id();


// 空かどうかチェック
function validRequire($str,$key){
    global $err_msg;
    if($str ===''){
        $err_msg[$key] = MSG01;
    }
}

// メールアドレスの形式かどうかチェック
function validEmail($str,$key){
    global $err_msg;
    if(filter_var($str, FILTER_VALIDATE_EMAIL) === false){
        $err_msg[$key] = MSG04;
    }
}

// 最小文字数チェック
function validMinLen($str,$key,$min=6){
    global $err_msg;
    if(mb_strlen($str)<$min){
        $err_msg[$key] = $min.MSG02;
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

// 半角数値チェック
function validNumber($str,$key){
    global $err_msg;
    if(!preg_match("/^[0-9]+$/",$str)){
        $err_msg[$key] = MSG13;
    }
}

// 文字列の一致チェック
function validMatch($str1,$str2,$key){
    global $err_msg;
    if($str1 !== $str2){
        $err_msg[$key] = MSG06;
    }
}

// メールアドレスの重複チェック
function validEmailDup($email){
    debug('メールアドレス重複チェック関数');
    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM users WHERE email = :email AND delete_flg=0';
        $data = array(':email'=>$email);
        $stmt = queryPost($dbh,$sql,$data);

        $result = $stmt->rowCount();
        if(!empty($result)){
            debug('このメールアドレスはすでにDBにあります');
            $err_msg['email'] = MSG14;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}



// DB接続準備
function dbConnect(){
    $dsn = 'mysql:dbname=rakusyu;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
}

function queryPost($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);
    $stmt_flg = $stmt->execute($data);

    if(!$stmt_flg){
        debug('クエリに失敗しました');
        debug('失敗したSQL：'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        return 0;
    }else{
        debug('クエリ成功');
        debug('成功したSQL：'.print_r($stmt,true));
        return $stmt;
    }
}


// slide_msgを一度だけ取得する
function getSlideMsg(){
    if(isset($_SESSION['suc_msg'])){
        $msg = $_SESSION['suc_msg'];
        unset($_SESSION['suc_msg']);
        return $msg;
    }
}

// ランダムキーの生成
function makeKey($len=8){
    $key='';
    $char='abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789';
    for($i=1;$i<$len;$i++){
        $key.=$char[mt_rand(0,mb_strlen($char)-1)];
    }
    return $key;
}

// メール送信関数
function sendMail($to,$from,$subject,$comment){
    // 日本語設定
    mb_language('Japanese');
    mb_internal_encoding('UTF-8');

    if(!empty($to) && !empty($subject) && !empty($comment)){
        $result = mb_send_mail($to,$subject,$comment,'From:'.$from);
        if($result){
            debug('メールの送信に成功しました');
            return true;
        }else{
            debug('メールの送信に失敗しました');
            return false;
        }
    }

}

// 習慣のデータをDBから取得する
function getHabitData($h_id){
    debug('習慣データを取得します');
    try{
        $dbh = dbCOnnect();
        $sql = 'SELECT id,habit,category_id,obstacle,if_plan,open_flg,
        create_date,delete_flg,user_id FROM habits WHERE id=:id AND delete_flg=0';
        $data = array(':id'=>$h_id);
        $stmt = queryPost($dbh,$sql,$data);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty($result)){
            return $result;
        }else{
            return 0;
        }
    } catch (Exception $e){
        error_log('エラーが発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}







// フォーム入力保持機能
// 1.DBにデータがあるか判定
// 2.バリデーションチェックOK、かつ、POST送信あり、かつ、PODT!===DBのとき、POSTデータを返す
// 2.上記以外は、DBデータを返す
// $flgがtrueならPOST,flaseならGETを返す

function getFormData($form_data,$key,$flg=true){
    if($flg){
        $method = $_POST;
    }else{
        $method = $_GET;
    }

    debug($key.'の判定です');
    global $err_msg;
    if($form_data[$key] !==''){
        if(!empty($err_msg[$key])){
            debug('ループ1');
            return sanitize($form_data[$key]);
        }else{
            if(isset($method[$key]) && $form_data[$key] !== $method[$key]){
                debug('ループ2');
                return sanitize($method[$key]);
            }else{
                debug('ループ3');
                return sanitize($form_data[$key]);
            }
        }

    }else{
        if(isset($method[$key])){
            debug('ループ4');
            return sanitize($method[$key]);
        }else{
            debug('ループ5');
        }
    }
}

// プロフィール情報取得関数
function getUserProf($u_id){
    
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id,email,name,age,job,pic FROM users
        WHERE id=:u_id AND delete_flg=0';
        $data = array(':u_id'=>$u_id);
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}





// 画像アップロード関数
// 1.$_filesに格納されているerrorをバリデーションチェック
// 2.新しいフォルダ先の名前を作成
// 3.権限を変更
// 4.画像ファイルを新しいフォルダに移動
// 5.新しい保存先のパスを返す

function uploadImg($file,$key){
    debug('画像アップロード処理開始');
    debug('$_FILESの中身：'.print_r($file,true));

    if(!empty($file['name'])){
        try{
            switch ($file['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                    throw new RuntimeException('その他のエラーが発生しました');
            }

            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_PNG,IMAGETYPE_JPEG],true)){
                throw new RuntimeException('画像形式が未対応です');
            }

            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'],$path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            chmod($path,0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：'.$path);
            return $path;
        } catch (Exception $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }

    }
}

// 習慣がユーザーのものか判定
function isHabitUser($h_id,$u_id){
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM habits WHERE id=:h_id AND 
        user_id=:u_id AND delete_flg=0';
        $data = array(':h_id'=>$h_id,':u_id'=>$u_id);
        $stmt = queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result)){
            return true;
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

// GETパラメータ付きURL生成関数
function appendGetParam($delete_key = array()){
    // debug('appendGetParamです');
    // $keyは取り除きたいGETパラメータの配列
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key=>$val){
            if(!in_array($key,$delete_key,true)){
                debug($key.'のパラメータを付けます');
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str,0,-1,'UTF-8');
        return $str;
    }else{
        // 表示すべきGETパラメータがない場合、空文字を返す
        return '';
    }
}

// 画像表示関数
function getImg($str){
    if(empty($str)){
        return 'uploads/sample.jpg';
    }else{
        return $str;
    }
}

// すべての習慣データの数を取得
function getHabitsListNum($c_id){
    try{
    $dbh = dbConnect();
    $sql = 'SELECT h.id FROM habits AS h LEFT JOIN 
    users AS u ON h.user_id = u.id 
    WHERE h.open_flg = 1 AND h.delete_flg=0';
    if(!empty($c_id)){
        $sql .=' AND h.category_id='.$c_id;
    }
    debug('SQL:'.$sql);
    $data = array();
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->rowCount();
    return $result;
}catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
}
}


// カテゴリーデータを取得する
function getCategoryList(){
    try{
    $dbh = dbConnect();
    $sql = 'SELECT id,name FROM category WHERE 
    delete_flg=0';
    debug('SQL:'.$sql);
    $data = array();
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetchAll();
    return $result;
}catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
}
}


// フォームのエラークラス出力関数
function getErr($str){
    global $err_msg;
    if(!empty($err_msg[$str])){
        return 'err';
    }
}


// サニタイズ関数
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}

// エラーメッセージ取得関数
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}





?>