<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログインページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// ログインロジック
// 1.post送信があるか判定
// 2.バリデーションチェック
// 3.DBのusersテーブルにselect文を実行。メールアドレスを渡して、該当するパスワードを取ってくる。
// 3-1.password_verifyでチェック
// 4.セッションに格納
// 5.マイページへ遷移

// ログイン認証
require('auth.php');


if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POST送信の中身：'.print_r($_POST,true));

    $email =$_POST['email'];
    $pass = $_POST['pass'];
    $keep_login = (!empty($_POST['keep_login'])) ? true : false; 

    validRequire($email,'email');
    validRequire($pass,'pass');

    if(empty($err_msg)){
        validMaxLen($email,'email');
    }
    if(empty($err_msg)){
        validEmail($email,'email');
    }

    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        try{
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email=:email AND delete_flg = 0';
            $data = array(':email'=>$email);
            $stmt = queryPost($dbh,$sql,$data);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('クエリ結果の中身：'.print_r($result,true));

        if(!empty($result) && password_verify($pass,array_shift($result))){
            debug('パスワードがマッチしました');
            // セッションを発行

            $_SESSION['user_id']= $result['id'];
            $_SESSION['login_limit'] = 60*60;
            if($keep_login){
                debug('ログイン保持にチェックがあります');
                $_SESSION['login_limit'] *= 24;
            }
            $_SESSION['login_date'] = time();
            
            // マイページへ遷移
            header("Location:mypage.php");
        }else{
            // パスワードがアンマッチ
            $err_msg['pass'] = MSG08;
        }
        
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }



    }

    // 後で消す
    debug('$err_msgの中身：'.print_r($err_msg,true));

}



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 操作完了後のメッセージスライド -->
    <div class="js-msg-slide">
        <p>
            <?php echo getSlideMsg();?>
            <?php debug('表示終了.$_SESSIONの中身：'.print_r($_SESSION,true));?>
        </p>
    </div>
    <?php require('header.php'); ?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">ログイン</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">メールアドレスとパスワードをご入力いただき、
                    「ログイン」ボタンを押してください</p>
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
                            <p class="text-field-msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">パスワード</label>
                            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?></p>
                        </div>  
                        
                    </div>
                    <div class="sub-button-wrap">
                        <div class="keep-button-wrap">
                            <input type="checkbox" name="keep_login"><span>ログイン状態を保持する</span>
                        </div>
                    </div>
                    
                    <div class="button-field-wrap">
                        <input type="submit" value="ログイン" class="main-button">                
                        <p class="pass-reminder-wrap">
                            パスワードを忘れた方は<a href="passReminderSend.php">コチラ</a>
                        </p>
                    </div>
                    
                </form>
            </div>
                
        </div>


    </main>
    <?php require('footer.php');?>

</body>
</html>