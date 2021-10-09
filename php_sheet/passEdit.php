<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' パスワード変更ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

debugLogStart();

// ログイン認証
require('auth.php');


// パスワード変更のロジック
// 1.post送信があるか判定
// 2.旧パスワードのチェック
// 3.パスワードのバリデーションチェック
// 4.usersテーブルにupdate文を実行

if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));

    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $retype_new_pass = $_POST['retype_new_pass'];

    validRequire($old_pass,'old_pass');
    validRequire($new_pass,'new_pass');
    validRequire($retype_new_pass,'retype_new_pass');

    if(empty($err_msg)){
        validMinLen($new_pass,'new_pass');
        validMaxLen($new_pass,'new_pass',$max=20);
    }
    
    if(empty($err_msg)){
        validHalf($new_pass,'new_pass');
        validMatch($new_pass,$retype_new_pass,'retype_new_pass');
    }

    if(empty($err_msg)){
        // 古いパスワードを確認する
        // user_idをsqlに渡してselect文を実行し、パスワードを取得
        // password_vefifyで判定
        try{
            $dbh = dbConnect();
            $sql = 'SELECT id,password FROM users WHERE id=:id AND delete_flg=0';
            $data = array(':id'=>$_SESSION['user_id']);
            $stmt = queryPost($dbh,$sql,$data);
            debug('$stmtの中身：'.print_r($stmt,true));

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            debug('$resultの中身：'.print_r($result,true));
            if(password_verify($old_pass,$result['password'])){
                debug('古いパスワードがDBとマッチしました');
            }else{
                debug('古いパスワードがDBとマッチしません');
                $err_msg['old_pass'] = MSG09;
            }
        }catch(Exception $e){
            error_log('エラーが発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
    if(empty($err_msg)){
        try{
            $sql = 'UPDATE `users` SET `password`=:pass WHERE `id`=:id';
            $data = array(':pass'=>password_hash($new_pass,PASSWORD_DEFAULT),':id'=>$_SESSION['user_id']);
            $stmt = queryPost($dbh,$sql,$data);

            debug('$stmtの中身：'.print_r($stmt,true));
            $_SESSION['suc_msg'] = SUC01;
            debug('パスワードの変更完了。$_SESSIONの中身：'.print_r($_SESSION,true));
            header("Location:mypage.php");
            exit();
        }catch(Exception $e){
            error_log('エラーが発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}



?>


<?php
$title="パスワード変更";
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
            <h1 class="main-title">パスワード変更</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">現在のパスワードと新しいパスワードをご入力いただき、
                    「パスワード変更」ボタンを押してください</p>
            </div>

            <div class="col1-wrap">
                <form action="" method="post">

                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">現在のパスワード</label>
                            <input type="password" name="old_pass"  value="<?php if(!empty($_POST['old_pass'])) echo $_POST['old_pass'];?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php echo getErrMsg('old_pass');?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">新しいパスワード</label>
                            <input type="password" name="new_pass"  value="<?php if(!empty($_POST['new_pass'])) echo $_POST['new_pass'];?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-info">
                                ※半角英字と数字を組み合わせた6〜20文字でご入力ください。
                            </p>
                            <p class="text-field-msg"><?php echo getErrMsg('new_pass');?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="text-field-wrap">
                        <div class="text-field-upper">
                            <span class="text-field-tag">必須</span>
                            <label for="" class="text-field-label">新パスワード(再入力)</label>
                            <input type="password" name="retype_new_pass"  value="<?php if(!empty($_POST['retype_new_pass'])) echo $_POST['retype_new_pass'];?>">
                        </div>
                        <div class="text-field-footer">
                            <p class="text-field-msg"><?php echo getErrMsg('retype_new_pass');?></p>
                        </div>  
                        
                    </div>
                    
                    <div class="button-field-wrap">
                        <input type="submit" value="パスワード変更" class="main-button">                
                    </div>
                    
                </form>
            </div>
                
        </div>


    </main>


<?php require('footer.php');?>
