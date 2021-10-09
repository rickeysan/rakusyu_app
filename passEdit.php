<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( パスワード変更ページ');
debug('(((((((((((((((((((((((((((((((((((((((');


// ログイン認証
require('auth.php');

// パスワード変更ロジック
// 1.post送信があるか判定
// 2.DBのusersテーブルのパスワードと一致しているか判定
// 3.新しいパスワードのバリデーションチェック
// 4.新しいパスワードでDBのusersテーブルにupdate文を実行


if(!empty($_POST)){
    
    debug('POST送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));

    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $retype_new_pass = $_POST['retype_new_pass'];

    validRequire($old_pass,'old_pass');
    validRequire($new_pass,'new_pass');
    validRequire($retype_new_pass,'retype_new_pass');

    if(empty($err_msg)){
        debug('古いパスワードを検証します');
        try{
            $dbh = dbConnect();
            $sql = 'SELECT password FROM users WHERE id=:u_id AND delete_flg=0';
            $data = array(':u_id'=>$_SESSION['user_id']);

            // クエリの実行
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                debug('$resultの中身：'.print_r($result,true));
                if(!password_verify($old_pass,$result['password'])){
                    $err_msg['old_pass'] = MSG09;
                }
            }else{
                $err_msg['common'] = MSG10;
            }
        }catch (Exception $e){
            error_log('エラーが発生:'.$e->getMessage());
            $err_msg['common'] = MSG10;
        }

        validPassAll($new_pass,'new_pass');
        validDiff($old_pass,$new_pass,'new_pass');
        validMatch($new_pass,$retype_new_pass,'retype_new_pass');
    }


    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        debug('update文を実行します');
        try{
            $sql = 'UPDATE users SET password=:pass WHERE id=:u_id AND delete_flg=0';
            $data = array(':pass'=>password_hash($new_pass,PASSWORD_DEFAULT),':u_id'=>$_SESSION['user_id']);

            // クエリの実行
            $stmt = queryPost($dbh,$sql,$data);

            if($stmt){
                debug('update文の実行に成功しました');
            }else{
                debug('update文の実行に失敗しました');
            }
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG10;
        }
    }

debug('$err_msgの中身：'.print_r($err_msg,true));
    
}




?>

<?php
$siteTitle = 'パスワード変更';
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

            <div class="page-info" style="line-height:30px;">
                <p>旧パスワードと新パスワードをご入力いただき、「変更する」ボタンを押してください</p>
            </div>
            <form action="" method="post" class="input-form" enctype="multipart/form-data">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="input-area">
                    <label>旧パスワード</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('old_pass');?>" name="old_pass" value="<?php if(!empty($_POST['old_pass'])) echo $_POST['old_pass'] ;?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['old_pass'])) echo $err_msg['old_pass'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area">
                    <label>新パスワード</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('new_pass');?>" name="new_pass" value="<?php if(!empty($_POST['new_pass'])) echo $_POST['new_pass'] ;?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['new_pass'])) echo $err_msg['new_pass'];?>
                        </div>
                    </div>
                </div>

                <div class="input-area">
                    <label>新パスワード（再入力）</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('retype_new_pass');?>" name="retype_new_pass"
                         value="<?php if(!empty($_POST['retype_new_pass'])) echo $_POST['retype_new_pass'] ;?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['retype_new_pass'])) echo $err_msg['retype_new_pass'];?>
                        </div>
                    </div>
                </div>
                
                

                <!-- 時間があるときに、パスワードを表示するためのボタンを実装する -->
                <div class="input-area submit-area">
                    <input type="submit" value="会員登録">
                </div>
            </form>
        </main>
        <aside class="sidemenu">
            <ul>
                <li><a href="mypage.php">マイページ</a></li>
                <li><a href="profEdit.php">プロフィール編集</a></li>
                <li><a href="index.php">検索</a></li>
                <li><a href="makeGoal.php">目標新規作成</a></li>
                <li><a href="passEdit.php">パスワード変更</a></li>
            </ul>
        </aside>
    </div>




</body>


<?php require('footer.php');?>


