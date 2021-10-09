<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug('テストページ1');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

debug('$_GETの値：'.print_r($_GET,true));






?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form action="" method="get">
        <label for="">メールアドレス
            <input type="text" name="email">
        </label>
        <input type="submit">
    </form>

    <form form method="get" class="search-bar">
        <div class="search-bar-item-wrap">
            <span class="search-bar-tag">カテゴリ</span>
            <select name="" id="">
                <option value="">運動</option>
                <option value="">食事</option>
                <option value="">勉強</option>
            </select>
        </div>
        <div class="search-bar-item-wrap">    
            <span class="search-bar-tag">表示順</span>
            <select name="time_sort" id="">
                <option value="0" selected>選択してください</option>
                <option value="1">新しい</option>
                <option value="2">古い</option>
            </select>
        </div>

        <div class="search-bar-item-wrap">   
            <input type="submit" value="検索する"> 
        </div>

    </form>

</body>
</html>