<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( 会員登録ページ');
debug('(((((((((((((((((((((((((((((((((((((((');

// 1.メールアドレスとパスワード、パスワード（再入力）をpost送信してもらう
// 2.バリデーションチェック
// 3.SQLでusersテーブルにinsert文を実行する

if(!empty($_POST)){
    debug('POST送信がされました'.print_r($_POST,true));

    // post変数を格納する
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $retype_pass = $_POST['retype_pass'];

    validRequire($email,'email');
    validRequire($pass,'pass');
    validRequire($retype_pass,'retype_pass');

    if(empty($err_msg)){
        validEmail($email,'email');
        validMinLen($pass,'pass');
        validMaxLen($retype_pass,'retype_pass');
    }

		if(empty($err_msg)){
			validHalf($pass,'pass');
		}
		if(empty($err_msg)){
			validMatch($pass,$retype_pass,'retype_pass');
		}

		if(empty($err_msg)){
			// Emial重複チェック
            validEmailDup($email);
		}
		
		if(empty($err_msg)){
			debug('バリデーションチェックOKです');
            debug('Insert文を実行します');

			try{
                $dbh = dbConnect();
                $sql = 'INSERT INTO users (email,password,
                create_date) VALUES (:email,:password,:create_date)';
                $data= array(':email'=>$email,
                ':password'=>password_hash($pass,PASSWORD_DEFAULT),
                ':create_date'=>date('Y-m-d H:i:s'));

                // クエリの実行
                $stmt = queryPost($dbh,$sql,$data);
                if($stmt){
                    debug('Insert文の実行に成功しました');
                    debug('マイページへ遷移します');
                    // マイページに遷移するので、セッション変数を用意する
                    $_SESSION['user_id'] = $dbh->lastInsertId();
                    $_SESSION['login_date'] = time();
                    $_SESSION['login_limit'] = 60*60;

                    header("Location:mypage.php");
                }else{
                    debug('Insert文の実行に失敗しました');
                }
            } catch (Exception $e){
                error_log('エラー発生：'.$e->getMessage());
            }

		}else{
			debug('バリデーションチェックNGです');
            debug('$err_msgの中身：'.print_r($err_msg,true));
		}
}




?>

<?php
$siteTitle = '会員登録';
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
                <p>メールアドレスとパスワードをご入力いただき、「会員登録する」ボタンを押してください</p>
            </div>
            <form action="" method="post" class="input-form">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="input-area">
                    <span class="form-tag">必須</span>
                    <label>メールアドレス</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('email');?>" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area">
                    <span class="form-tag">必須</span>
                    <label>パスワード</label>
                    <div class="input-section">
                        <input type="text" class="<?php echo getErr('pass');?>" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area">
                    <span class="form-tag">必須</span>
                <label>パスワード（再入力）</label>
                <div class="input-section err-msg">
                    <input type="text" class="<?php echo getErr('retype_pass');?>" name="retype_pass" value="<?php if(!empty($_POST['retype_pass'])) echo $_POST['retype_pass'];?>">
                    <div class="area-msg">
                        <?php if(!empty($err_msg['retype_pass'])) echo $err_msg['retype_pass'];?>
                    </div>
                </div>
                </div>      
                <!-- 時間があるときに、パスワードを表示するためのボタンを実装する -->
                <div class="input-area submit-area">
                    <input type="submit" value="会員登録">
                </div>
            </form>
        </div>


    </main>




</body>








