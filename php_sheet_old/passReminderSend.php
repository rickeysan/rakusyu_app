<?php

require('function.php');

debug('( パスワードリマインダー送信ページ');

// ログイン認証は不要


// 1-1.post送信があるか判定
// 1-2.メールアドレスのバリデーションチェック
// 2.メールアドレスがDBのuserテーブルに登録されているか判定
// 3.ランダムキーを生成
// 4.メール送信のための変数を準備
// 5.ランダムキーをのせて、メールを送信
// 6.セッション変数にauth_keyとkey_kimitを格納する
// 7.パスワードリマインダー受信ページへ遷移



if(!empty($_POST)){
    debug('POST送信がされました');
    debug('POST送信の中身：'.print_r($_POST,true));

    $email = $_POST['email'];

    validRequire($email,'email');
    if(empty($err_msg)){
        validMaxLen($email,'email');
    }
    if(empty($err_msg)){
        validEmail($email,'email');
    }

    if(empty($err_msg)){
        debug('EmailのバリデーションチェックOKです');
        
        try{
            $dbh = dbConnect();
            $sql = 'SELECT count(*) FROM users WHERE email=:email AND
            delete_flg=0';
            $data = array(':email'=>$email);

            // クエリの実行
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                debug('SELECT文の実行に成功しました');
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                debug('$resultの中身：'.print_r($result,true));
                if($stmt && array_shift($result)){
                    debug('DBにEmailが存在します');
                    
                    $auth_key = makeRandomKey();
                    
                    // メール送信の準備
                    $to = $email;
                    $from = 'rickeysan95@gmail.com';
                    $subject = '楽々習慣　｜　パスワード再発行の手続き';
                    $comment = <<<EOT
パスワード再発行の手続き 

本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力いただくとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：https://localhost/web_op/passReminderRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願いいたします。
https://localhost/web_op/passReminderRecieve.php


※このメールはシステムより自動送信されています。
　返信は無効となりますので、ご了承ください。
　ご不明な点がございましたら、下記へお問合せください。
EOT;
                    $result = sendMail($to,$subject,$comment,'From:'.$from);
                    if($result){
    
                        $_SESSION['auth_key'] = $auth_key;
                        $_SESSION['key_limit'] = time()+ 60 * 30;
                        $_SESSION['email'] = $email;
                        debug('$_SESSIONの中身：'.print_r($_SESSION,true));
    
                        header("Location:passReminderRecieve.php");                        
                    }else{
                        // メールの送信失敗
                        $err_msg['common'] = MSG10;
                    }

                }else{
                    debug('SELECT文の実行に失敗したか、DBにそのEmailは存在しません');
                    $err_msg['common'] = MSG10;
                }
            } 
        }catch (Exception $e){
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG11;
            }
    }

}








?>


<?php
$siteTitle = 'パスワードリマインダー送信';
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
                <p>登録しているメールアドレスご入力いただき、「再発行メールを送信」ボタンを押してください</p>
            </div>
            <form action="" method="post" class="input-form">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="input-area">
                    <label>メールアドレス</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('email');?>" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
                        </div>
                    </div>
                </div>
                
                
                <div class="input-area submit-area">
                    <input type="submit" value="再発行メールを送信">
                </div>
            </form>
        </div>


    </main>

   

</body>










