<?php


debug('ログイン認証です');
debug('$_SESSIONの中身：'.print_r($_SESSION,true));


if(!empty($_SESSION['login_date'])){
    debug('ログインしています');

    if($_SESSION['login_date'] + $_SESSION['login_limit'] > time()){
        debug('ログイン有効期限内です');
        $_SESSION['login_date'] = time();
        debug('ページ遷移はしません');

        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            header("Location:mypage.php");
        }
    }else{
        debug('ログイン有効期限が切れています');
        debug('ログインページへ遷移します');
        // セッションを完全に削除
        $_SESSION = array();
        setcookie(session_name(), '', time()-1, '/');
        session_destroy();
        
        header("Location:login.php");
    }

}else{
    debug('ログインしていません');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        debug('ログインページへ遷移します');
        header("Location:login.php");
    }
}







?>