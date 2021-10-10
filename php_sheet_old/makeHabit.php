<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( 習慣の新規作成・編集画面');
debug('(((((((((((((((((((((((((((((((((((((((');

// debug('$_SERVERの中身；'.print_r($_SERVER    ,true));

// debug('basename($_SERVERの中身)；'.print_r(basename($_SERVER['PHP_SELF']),true));

// ログイン認証
require('auth.php');




?>

<?php
$siteTitle = '習慣の新規作成・編集';
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
        <div class="main-wrap main-2colum">
        <main class="main main-2col">
            <section class="habits-section">

            <div class="page-info" style="line-height:30px;">
                <p>(1) 達成したい目標を設定する<br>
                    (2) 最も障害となりそうな要因を考える<br>
                    (3) その障害に直面したとき、どうするか考える</p>
            </div>
            <form action="" method="post" class="input-form">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="textarea-area input-area">
                    <label>目標</label>
                    <div class="input-section">
                        <textarea type="text"  class="habit-input" class="<?php echo getErr('email');?>" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>"></textarea>
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
                        </div>
                    </div>
                </div>
                
                <div class="textarea-area input-area">
                    <label>最大の障害</label>
                    <div class="input-section">
                        <textarea type="text" class="difficult-input" class="<?php echo getErr('pass');?>" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>"></textarea>
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?>
                        </div>
                    </div>
                </div>
                
                <div class="textarea-area input-area">
                    <label>If-Thenルール</label>
                    <div class="input-section">
                        <textarea type="text" class="if-then-input" class="<?php echo getErr('pass');?>" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>"></textarea>
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass'];?>
                        </div>
                    </div>
                </div>

                <div class="check-box-wrap">
                    <input type="checkbox" class="open-check"><span>この投稿を一般公開する</span>
                </div>

                <div class="input-area submit-area">
                    <input type="submit" value="新規登録する">
                </div>
            </form>
        </section>
        
    </main>
</div>

        <?php require('sidebar.php'); ?>
   

</body>






