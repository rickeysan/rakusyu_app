<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' パスワード再発行キー送信ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// パスワード再発行キーの送信
// 1.post送信があるか判定
// 2.メールアドレスが登録されているか判定
// 3.再発行キーを生成
// 4.メールを送信
// 5.セッションに再発行キーと期限を格納
// 6.入力ページに遷移


if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));

    $email = $_POST['email'];

    validRequire($email,'email');
    if(empty($err_msg)){
        try{
            $dbh = dbConnect();
            $sql = 'SELECT id FROM users WHERE email=:email AND delete_flg=0';
            $data = array(':email'=>$email);
            $stmt = queryPost($dbh,$sql,$data);

            $result = $stmt->rowCount();

            if($result){
                debug('OK：メールアドレスはDBに登録されています');
            }else{
                debug('NG：メールアドレスはDBに登録されていません');
                $err_msg['email'] = MSG10;
            }

        } catch (Exception $e){
            error_log('エラーが発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

    if(empty($err_msg)){
        $auth_key = makeKey();
        $to = $email;
        $subject = 'パスワード再発行認証';
        $comment =<<<EOT
本メールアドレス宛てにパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力いただくと、パスワードが再発行されます。

パスワード再発行認証キー入力ページ：
認証キー：{$auth_key}
※認証キーを再発行されたい場合は、下記ページより再度の再発行をお願いします。
passReminderSend.php

///////////////////////////////////////////////
ウェブカツマーケットカスタマーセンター
URL
E-mail:
///////////////////////////////////////////////          

EOT;
        $from = 'rickeysan95@gmail.com';

        $result = sendMail($to,$from,$subject,$comment);
        
        if($result){            
            $_SESSION['auth_key'] = $auth_key;
            $_SESSION['auth_key_limit'] = 60*60 + time();
            $_SESSION['user_email'] = $email;
            $_SESSION['suc_msg'] = SUC02;
            debug('$_SESSIONの中身３：'.print_r($_SESSION,true));

            header("Location:passReminderRecieve.php");
            return;
        }else{
            $err_msg['common'] = MSG07;
        }
    }

}

?>




<?php
$title="パスワード再発行メール送信";
require('head.php');
?>
    <!-- 操作完了後のメッセージスライド -->
    <div class="js-msg-slide">
        <p>
            <?php echo getSlideMsg();?>
        </p>
    </div>

    <?php require('header.php');?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">パスワード再発行キー送信</h1>
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
                            <label for="" class="text-field-label">メールアドレス</label>
                            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php echo getErrMsg('email');?></p>
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

