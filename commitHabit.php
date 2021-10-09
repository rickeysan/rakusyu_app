<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( 習慣のログ更新・確認画面');
debug('(((((((((((((((((((((((((((((((((((((((');

// debug('$_SERVERの中身；'.print_r($_SERVER    ,true));

// debug('basename($_SERVERの中身)；'.print_r(basename($_SERVER['PHP_SELF']),true));

// ログイン認証
require('auth.php');




?>

<?php
$siteTitle = '習慣のログ更新・確認';
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
                <p>(1) 達成したい目標を設定する<br>
                    (2) 最も障害となりそうな要因を考える<br>
                    (3) その障害に直面したとき、どうするか考える</p>
            </div>
            <form action="" method="post" class="input-form">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="input-area">
                    <label>メールアドレス</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('email');?>" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area">
                    <label>パスワード</label>
                    <div class="input-section">
                        <input type="text" class="<?php echo getErr('pass');?>" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area submit-area">
                    <input type="submit" value="新規登録する">
                </div>
            </form>
        </div>


    </main>

   

</body>






