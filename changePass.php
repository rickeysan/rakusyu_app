<?php

require('function.php');

debug('( パスワード変更画面');

// ログイン認証
require('auth.php');

// 1.post送信があるか判定
// 2.バリデーションチェック
// 3.DBにupdate文を実行
// 4.マイページへ遷移

if(!empty($_POST)){
    debug('POST送信があります');
    debug('$_POST送信の中身：'.print_r($_POST,true));

    // post変数を格納する
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $retype_new_pass = $_POST['retype_new_pass'];

    validRequire($old_pass,'old_pass');
    validRequire($new_pass,'new_pass');
    validRequire($retype_new_pass,'retype_new_pass');
    
    if(empty($err_msg)){
        validPassAll($old_pass,'old_pass');
        validPassAll($new_pass,'new_pass');
    }
    // 古いパスワードがあっているか判定する
    try{
        debug('古いパスワードがあっているか判定します');
        $dbh = dbConnect();
        $sql = 'SELECT password FROM users WHERE id=:id AND delete_flg=0';
        $data = array(':id'=>$_SESSION['user_id']);
        
        // クエリの実行
        $stmt = queryPost($dbh,$sql,$data);
        if($stmt){
            debug('SELECT文の実行に成功しました');
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('$resultの中身：'.print_r($result,true));
            
            if(!password_verify($old_pass,array_shift($result))){
                debug('古いパスワードがDBと一致しません');
                $err_msg['old_pass'] = MSG09;
            }else{
                debug('古いパスワードがDBと一致しました');
            }
        }else{
            debug('SELECT文の実行に失敗しました');
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
    
    if(empty($err_msg)){
        validMatch($new_pass,$retype_new_pass,'retype_new_pass');
    }

    if(empty($err_msg)){
        if($new_pass === $old_pass){
            $err_msg['new_pass'] = MSG11;
        }
    }

    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET password = :pass WHERE
            id=:id AND delete_flg=0';
            $data = array(':pass'=>password_hash($new_pass,PASSWORD_DEFAULT), ':id'=>$_SESSION['user_id']);

            // クエリの実行
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                debug('update文の実行に成功しました');
                debug('マイページへ遷移します');
                header("Location:mypage.php");
            }else{
                debug('update文の実行に失敗しました');
                $err_msg['common'] = MSG10;
            }
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG10;
        }

    }


    debug('$err_msgの中身：'.print_r($err_msg,true));

}









?>





<?php
$siteTitle = '会員登録';
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <!-- Main -->
    <main class="main">
        <form action="" method="post">
            <div class="area-msg">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
            </div>

            <label>
                旧パスワード
                <input type="text" name="old_pass" value="<?php if(!empty($_POST['old_pass'])) echo $_POST['old_pass'];?>">
            </label>
            <div class="area-msg">
                <?php if(!empty($err_msg['old_pass'])) echo $err_msg['old_pass'];?>
            </div>

            <label>
                新パスワード
                <input type="text" name="new_pass" value="<?php if(!empty($_POST['new_pass'])) echo $_POST['new_pass'];?>">
            </label>
            <div class="area-msg">
                <?php if(!empty($err_msg['new_pass'])) echo $err_msg['new_pass'];?>
            </div>

            <label>
                新パスワード（再入力）
                <input type="text" name="retype_new_pass" value="<?php if(!empty($_POST['retype_new_pass'])) echo $_POST['retype_new_pass'];?>">
            </label>
            <div class="area-msg">
                <?php if(!empty($err_msg['retype_new_pass'])) echo $err_msg['retype_new_pass'];?>
            </div>


            <!-- 時間があるときに、パスワードを表示するためのボタンを実装する -->
            
            <input type="submit" value="変更する">
        </form>


    </main>




</body>













