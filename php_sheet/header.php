
<header id="header">
        <div class="header-inner">

            <h1 class="site-title">
                <a href="index.php">
                    <img src="img/logo.png" alt="">
                </a>
            </h1>
            
            <ul class="header-nav">
                <?php if(empty($_SESSION['user_id'])){;?>
                    <li><a href="login.php">ログイン</a></li>
                    <li><a href="signup.php">会員登録</a></li>
                <?php }else{ ;?>
                    <li><a href="mypage.php">マイページ</a></li>
                    <li><a href="logout.php">ログアウト</a></li>
                <?php } ?>
            </ul>
            
        </div>
</header>


