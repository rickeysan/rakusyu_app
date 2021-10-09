<?php

require('function.php');

// ログアウトの手順（セッション変数を完全に削除する）
// 1.セッション変数の削除
// 2.クッキーの削除
// 3.セッションファイルの削除

$_SESSION = array();

setcookie(session_name(),'',time()-1);

session_destroy();


// ログイン認証（ログアウト用）
require('auth.php');













?>