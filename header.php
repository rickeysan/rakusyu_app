<header class="header">
            <h1 class="site-title"><a href="top.php">
              <img src="img/logo.png" alt="">
            </a></h1>
            <ul class="header-menu">
                <?php if(empty($_SESSION['login_date'])){ ?>
                  <li><a href="signup.php">新規登録</a></li>
                  <li><a href="login.php">ログイン</a></li>
								<?php } else { ?>
									<li><a href="logout.php">ログアウト</a></li>
                  <li><a href="mypage.php">マイページ</a></li>
								<?php } ?>
            </ul>
</header>
