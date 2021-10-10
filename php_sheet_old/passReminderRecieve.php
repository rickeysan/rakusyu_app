<?php

require('function.php');

debug('( パスワードリマインダー送信ページ');

// ログイン認証は不要


// 1.post送信があるか判定
// 1-2.セッションがあるか判定（パスワード再発行キー送信ページからの遷移かどうか）
// 2.$_SESSION変数を取り出す
// 3.auth_keyがあっているか判定
// 4.key_limitが現在時刻内か判定
// 5.新しいパスワードを生成
// 5.DBにupdate文を実行する
// 6.メール送信のための変数を準備
// 7.メールを送信
// 8.セッション変数を削除する
// 9.ログインページに遷移する


if(!empty($_POST)){
    debug('POST送信があります');
    if(empty($_SESSION['auth_key'])){
        debug('パスワード再発行キー送信ページからのページ遷移ではありません');
        header("Location:passReminderSend.php");
    }
    $auth_key = $_POST['auth_key'];
    validRequire($auth_key,'auth_key');
    if(empty($err_msg)){
        if($auth_key !== $_SESSION['auth_key']){
            $err_msg['auth_key'] = MSG12;
        }
    }
    if(empty($err_msg)){
        if($_SESSION['key_limit'] < time()){
            $err_msg['auth_key'] = MSG13;
        }
    }

    if(empty($err_msg)){
        debug('再発行キーのバリデーションチェックOKです');
        $new_pass = makeRandomKey();
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET password=:pass WHERE email=:email
            AND delete_flg=0';
            $data = array(':pass'=>password_hash($new_pass,PASSWORD_DEFAULT),
                        ':email'=>$_SESSION['email']);

            // クエリの実行
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                debug('UPDATE文の実行に成功しました');
                        
                $to = $_SESSION['email'];
                $from = 'rickeysan95@gmail.com';
                $subject = '楽々習慣　｜　パスワード再発行完了のお知らせ';
                $comment = <<<EOT
パスワード再発行完了のお知らせ 

パスワード再発行が完了しました。

新パスワード：{$new_pass}
ログイン後は、パスワードの変更をお願いします。


※このメールはシステムより自動送信されています。
　返信は無効となりますので、ご了承ください。
　ご不明な点がございましたら、下記へお問合せください。
EOT;                
                $result = sendMail($to,$subject,$comment,'From:'.$from);

                if($result){
                    $_SESSION = array();
                    debug('$_SESSIONの中身：'.print_r($_SESSION,true));

                    header("Location:login.php");                        
                }else{
                    // メールの送信失敗
                    $err_msg['common'] = MSG10;
                }



            }else{
                debug('UPDATE文の実行に失敗しました');
                $err_msg['common'] = MSG10;
            }
        } catch (Exception $e){
            error_log('エラーが発生'.$e->getMessage());
            $err_msg['common'] = MSG11;
        }



    }else{
        debug('再発行キーのバリデーションチェックNGです');
    }


    debug('$err_msgの中身：'.print_r($err_msg,true));
}







?>


<?php
$siteTitle = 'パスワードリマインダー受信';
require('head.php');
?>


<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>
    <!-- サブヘッダー -->
    <div class="sub-header">
        <h2 class="page-title"><?php echo $siteTitle;?></h2>
    </div>
<!-- Main -->
<main class="main">
        <div class="main-wrap">

            <div class="page-info" style="line-height:30px;">
                <p>メールアドレスに届いた再発行キーを入力していただき、「パスワード再発行」ボタンを押してください</p>
            </div>
            <form action="" method="post" class="input-form">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="input-area">
                    <label>再発行キー</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('auth_key');?>" name="auth_key" value="<?php if(!empty($_POST['auth_key'])) echo $_POST['auth_key'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['auth_key'])) echo $err_msg['auth_key'];?>
                        </div>
                    </div>
                </div>
                
                
                <div class="input-area submit-area">
                    <input type="submit" value="パスワード再発行">
                </div>
            </form>
        </div>


    </main>

   

</body>




