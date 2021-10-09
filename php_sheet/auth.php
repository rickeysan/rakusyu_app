<?php
// ログインチェック関数のロジック
// 1.$_SESSION['user_id']に値があるか判定
// 1-2.値がない場合、かつ、呼び出し元がログインページでない場合、ログインページに遷移
// 2.有効期限の判定
// 3.有効であれば、$_SESSION['login_date']を更新
// 4.無効であり、かつ、呼び出し元がログインページでない場合、ログインページに遷移

// debug('ログインチェック関数$_SESSION'.print_r($_SESSION,true));
// debug('ログインチェック関数$_SERVER'.print_r($_SERVER,true));
debug('-------ログインチェック関数-----------');
debug('$_SESSIONの中身：'.print_r($_SESSION,true));

if(!empty($_SESSION['user_id'])){
    debug('ログイン済みユーザーです');
    
    if($_SESSION['login_date'] + $_SESSION['login_limit'] > time()){
        debug('セッションは有効期限内です');
        $_SESSION['login_date'] = time();
        if(basename($_SERVER['PHP_SELF']) === 'login.php'){
            header('Location:mypage.php');    
        }
    }else{
        debug('セッションの有効期限は切れています');
        if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
            header('Location:login.php');
        }
    }
}else{
    debug('このユーザーはログインしていません');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header('Location:login.php');
    }
}









?>