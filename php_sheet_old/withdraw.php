<?php

require('function.php');

debug('( 退会ページ');

// ログイン認証
require('auth.php');

// 退会ロジック
// 1.post送信がされたか判定
// 2.DBにupdate文を実行
// 3.セッションを削除して、会員登録画面に遷移

if(!empty($_POST)){
    debug('POST送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));

    try{
        $dbh = dbConnect();
        $sql = 'UPDATE users SET delete_flg=1 WHERE id=:id AND delete_flg=0';
        $data = array(':id'=>$_SESSION['user_id']);

        // クエリの実行
        $stmt = queryPost($dbh,$sql,$data);
        debug('$stmtの中身：'.print_r($stmt,true));

        if($stmt){
            debug('UPDATE文の実行に成功しました');
            // セッションを完全に削除
            $_SESSION = array();
            setcookie(session_name(), '', time()-1, '/');
            session_destroy();

            debug('セッションを完全に削除しました。会員登録画面に遷移します');
            header("Location:signup.php");

        }else{
            debug('UPDATE文の実行に失敗しました');
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }


}


?>


<?php
$siteTitle = '退会画面';
require('head.php');
?>

<body>
    <!-- ヘッダー -->
    <?php require('header.php'); ?>

    <!-- Main -->
    <main class="main">
        <form action="" method="post">
            
            <input type="submit" value="退会する" name="sample">
        </form>


    </main>




</body>














