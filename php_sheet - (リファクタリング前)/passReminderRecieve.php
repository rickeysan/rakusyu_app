<?php

require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' パスワード再発行キー受信ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug('$_SESSIONの中身４：'.print_r($_SESSION,true));



// 再発行ページからの遷移か判定
if(empty($_SESSION['user_email'])){
    debug('不正な値が入りました');
    header("Location:login.php");
}

// 再発行キーの受信のメソッド
// 1.post送信があるか判定
// 2.再発行キーが正しいか判定
// 3.再発行キーが有効期限内か判定
// 4.新規パスワードを発行
// 4-2.DBのuserテーブルにupdate文を実行
// 5.メールに送信
// 6.ログイン画面に遷移

if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POST送信の中身：'.print_r($_POST,true));

    $auth_key = $_POST['auth_key'];

    validRequire($auth_key,'auth_key');

    if(empty($err_msg)){
        if($_SESSION['auth_key'] !== $auth_key){
            $err_msg['auth_key'] = MSG11;
        }
    }
    if(empty($err_msg)){
        if($_SESSION['auth_key_limit'] < time()){
            $err_msg['auth_key'] = MSG12;
        }
    }
    if(empty($err_msg)){
        debug('認証キーのバリデーションチェックOKです');
        $new_pass = makeKey();
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET password=:pass WHERE email=:email AND delete_flg=0';
            $data = array(':email'=>$_SESSION['user_email'],':pass'=>password_hash($new_pass,PASSWORD_DEFAULT));

            $stmt = queryPost($dbh,$sql,$data);

            $result = $stmt->rowCount();
            if(!empty($result)){
                debug('OK:DBの変更に成功しました');
            }else{
                debug('NG:DBの変更に失敗しました');
            }

        } catch (Exception $e){
            error_log('エラーが発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }


        $to = $_SESSION['user_email'];
        $subject = 'パスワード再発行完了通知';
        $comment =<<<EOT
本メールアドレス宛にパスワードの再発行をいたしました。
下記のURLにて再発行パスワードをご入力いただき、ログインください。

ログインページ：http://localhost:8888/webservice_practice07/login.php
再発行パスワード：{$new_pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////
ウェブカツマーケットカスタマーセンター
URL  http://webukatu.com/
E-mail info@webukatu.com
////////////////////////////////////////
          
EOT;
        $from = 'rickeysan95@gmail.com';

        $result = sendMail($to,$from,$subject,$comment);
        if($result){
            $_SESSION = array();
            $_SESSION['suc_msg'] = SUC03;
            header("Location:login.php");
            return;
        }else{
            $err_msg['common'] = MSG07;
        }
    }
}





?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード変更</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 操作完了後のメッセージスライド -->
    <div class="js-msg-slide">
        <p>
            <?php echo getSlideMsg();?>
            <?php debug('スライドメッセージの表示終了.$_SESSIONの中身：'.print_r($_SESSION,true));?>
            <!-- パスワードの変更が完了しました! -->
        </p>
    </div>

    <?php require('header.php');?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">パスワード再発行キー入力</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">メールアドレスとパスワードをご入力いただき、
                    「送信する」ボタンを押してください</p>
            </div>

            <div class="col1-wrap">
                <form action="" method="post">

                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">再発行キー</label>
                            <input type="text" name="auth_key" value="<?php if(!empty($_POST['auth_key'])) echo $_POST['auth_key'];?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php if(!empty($err_msg['auth_key'])) echo $err_msg['auth_key'];?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="button-field-wrap">
                        <input type="submit" value="再発行" class="main-button">                
                    </div>
                    
                </form>
            </div>
                
        </div>


    </main>
    <?php require('footer.php');?>
</body>
</html>