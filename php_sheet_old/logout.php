<?php

require('function.php');

debug('( ログアウトページ');

debug('ログアウトして、ログインページへ遷移します');
// ログイン認証
require('auth.php');

$_SESSION = array();

setcookie(session_name(), '', time()-1, '/');

session_destroy();

// ログインページへ遷移
header("Location:login.php");





?>