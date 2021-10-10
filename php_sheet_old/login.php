<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( ログインページ');
debug('(((((((((((((((((((((((((((((((((((((((');

// debug('$_SERVERの中身；'.print_r($_SERVER    ,true));

// debug('basename($_SERVERの中身)；'.print_r(basename($_SERVER['PHP_SELF']),true));

// ログイン認証
require('auth.php');

// 1.メールアドレスとパスワードをpost送信してもらう
// 2.バリデーションチェック
// 3.SQLでusersテーブルにselect文を実行する
// 4.ログイン保持にチェックがあるか確認
// 5.セッションに変数を入れす

if(!empty($_POST)){
    debug('POST送信がされました'.print_r($_POST,true));
    debug('POST送信の中身：'.print_r($_POST,true));

    // post変数を格納する
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $login_keep = (!empty($_POST['login_keep'])) ? true : false;
    debug('login_keepの中身：'.$login_keep);

    validRequire($email,'email');
    validRequire($pass,'pass');

    if(empty($err_msg)){
        validEmail($email,'email');
        validMinLen($pass,'pass');
    }

		if(empty($err_msg)){
			$result = getUserIdAndPass($email,$pass);
            debug('$resultの中身；'.print_r($result,true));
		}
		
		if(empty($err_msg)){
			debug('バリデーションチェックOKです');
            $sesLimit = 60*60;

            // 4.ログイン保持にチェックがあるか確認
            if($login_keep){
                debug('ログイン保持にチェックがあります');
                $sesLimit = 60*60*24*30;
            }else{
                debug('ログイン保持にチェックはありません');
            }

            $_SESSION['user_id'] = $result['id'];
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;

            debug('$_SESSIONの中身：'.print_r($_SESSION,true));
            debug('マイページへ遷移します');
            header("Location:mypage.php");


		}else{
			debug('バリデーションチェックNGです');
            debug('$err_msgの中身：'.print_r($err_msg,true));
		}
}




?>

<?php
$siteTitle = 'ログイン';
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
                <p>メールアドレスとパスワードをご入力いただき、「ログイン」ボタンを押してください</p>
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
                
                <div class="input-area">
                    <label>パスワード</label>
                    <div class="input-section">
                        <input type="text" class="<?php echo getErr('pass');?>" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area submit-area">
                    <input type="submit" value="ログイン">
                    <p>パスワードを忘れた方は<a href="passReminderSend.php">コチラ</a></p>
                </div>
            </form>
        </div>


    </main>

   

</body>








