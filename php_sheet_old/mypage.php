<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( マイページ');
debug('(((((((((((((((((((((((((((((((((((((((');

// debug('$_SERVERの中身；'.print_r($_SERVER,true));

// debug('basename($_SERVERの中身)；'.print_r(basename($_SERVER['PHP_SELF']),true));

// ログイン認証
require('auth.php');


// debug('$_COOKIEの中身：'.print_r($_COOKIE,true));

?>

<?php
$siteTitle = 'マイページ';
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
    <div class="main-wrap main-2column">
        <main class="main main-2col">
            <section class="habits-section">
                <h2 class="section-title">My目標一覧</h2>
                <div class="habits-list">
                    <ul>
                        <li>
                            <div class="habit-list-upper">
                                <div class="habit-item goal-item">
                                    <span>目標</span>
                                    <p>毎日プログラミングを3時間勉強する</p>
                                </div>
                                <div class="habit-item difficult-item">
                                    <span>想定される障害</span>
                                    <p>YouTubeを見たくなる</p>
                                </div>
                                <div class="habit-item if-then-item">
                                    <span>If-Thenルール</span>
                                    <p>もし動画を見たいと思ったら、マッサージガンを首にあてる</p>
                                </div>
                                
                            </div>
                            <div class="habit-list-footer">
                                <a href="">記録の確認・更新</a>
                                <a href="">目標の編集</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            <section class="favorite-section">
                <h2 class="section-title">お気に入り一覧</h2>
                <div class="other-habits-list">
                    <ul>
                        <li>
                            <div class="other-habit-left">
                                <span>リッキーさん</span>
                                <img src="" alt="">
                            </div>
                            <div class="other-habit-right">
                                <div class="habit-item goal-item">
                                    <span>目標</span>
                                    <p>毎日プログラミングを3時間勉強する</p>
                                </div>
                                <div class="habit-item difficult-item">
                                    <span>想定される障害</span>
                                    <p>YouTubeを見たくなる</p>
                                </div>
                                <div class="habit-item if-then-item">
                                    <span>If-Thenルール</span>
                                    <p>もし動画を見たいと思ったら、マッサージガンを首にあてる</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </main>
        <?php require('sidebar.php'); ?>
    </div>




</body>








