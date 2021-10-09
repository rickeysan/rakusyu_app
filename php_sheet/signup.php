<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 会員登録ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');


// 会員登録ロジック
// 1.post送信があるか判定
// 2.入力値のバリデーションチェック
// 3.DBのusersテーブルにinsert文を実行
// 4.セッションを発行
// 5.マイページへ遷移

if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POST送信の中身：'.print_r($_POST,true));

    $email =$_POST['email'];
    $pass = $_POST['pass'];
    $retype_pass = $_POST['retype_pass'];

    validRequire($email,'email');
    validRequire($pass,'pass');
    validRequire($retype_pass,'retype_pass');

    if(empty($err_msg)){
        validMaxLen($email,'email');
        validMaxLen($pass,'pass',$max=20);
        validMinLen($pass,'pass');
    }
    if(empty($err_msg)){
        validEmail($email,'email');
        validHalf($pass,'pass');
    }
    if(empty($err_msg)){
        validEmailDup($email);
        validMatch($pass,$retype_pass,'retype_pass');
    }

    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO users (email,password,age,create_date) VALUES 
        (:email,:pass,:age,:create_date)';
        $data = array(':email'=>$email,':pass'=>password_hash($pass,PASSWORD_DEFAULT),
                        ':age'=>'',':create_date'=>date('Y-m-d H:i:s'));
        $result = queryPost($dbh,$sql,$data);
        
        if($result){
            // セッションを発行
            $_SESSION['user_id'] = $dbh->lastInsertId();
            $_SESSION['login_limit'] = 60*60;
            $_SESSION['login_date'] = time();
            // マイページへ遷移
            header("Location:mypage.php");
        }else{
            $err_msg['common'] = MSG07;
        }
        
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}


?>



<?php
$title="会員登録";
require('head.php');
?>


    <?php require('header.php'); ?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">会員登録</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">メールアドレスとパスワードをご入力いただき、
                    「会員登録する」ボタンを押してください</p>
            </div>

            <div class="col1-wrap">
                <form action="" method="post">

                    <div class="text-field-wrap">
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php if(!empty($err_msg['common'])) echo $err_msg['common'];?></p>
                        </div>  
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">メールアドレス</label>
                            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo sanitize($_POST['email']);?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php echo getErrMsg('email');?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">パスワード</label>
                            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo sanitize($_POST['pass']);?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-info">
                                ※半角英字と数字を組み合わせた6〜20文字でご入力ください。
                            </p>
                            <p class="text-field-msg"><?php echo getErrMsg('pass');?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">パスワード（再入力）</label>
                            <input type="password" name="retype_pass" value="<?php if(!empty($_POST['retype_pass'])) echo sanitize($_POST['retype_pass']);?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php echo getErrMsg('retype_pass');?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="button-field-wrap">
                        <input type="submit" value="会員登録" class="main-button">                
                    </div>
                    
                </form>
            </div>
                
        </div>


    </main>

    <?php require('footer.php');?>
