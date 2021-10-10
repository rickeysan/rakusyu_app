<?php

// 共通変数と関数の読み込み
require('function.php');

debug('(((((((((((((((((((((((((((((((((((((((');
debug('( プロフィール編集ページ');
debug('(((((((((((((((((((((((((((((((((((((((');


// ログイン認証
require('auth.php');


$dbUserData = getUser($_SESSION['user_id']);
debug('$dbUserDataの中身：'.print_r($dbUserData,true));

// プロフィール編集機能
// 1.POST送信があるか確認
// 2.バリデーションチェック
// 3.DBのuserテーブルの値と違うかを判定
// 4.DBにupdate文を実行する

if(!empty($_POST)){
    debug('POST送信があります'.print_r($_POST,true));
    debug('FILE情報：'.print_r($_FILES,true));
    $email = $_POST['email'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $job = $_POST['job'];
    
    

    if($email !== $dbUserData['email']){
        validRequire($email,'email');
        if(empty($err_msg['email'])){
            validMaxLen($email,'email');
        }
        if(empty($err_msg['email'])){
            validEmail($email,'email');
        }
    }

    if($name !== $dbUserData['name']){
        validMaxLen($name,'name');
    }
    
    
    if($age !== $dbUserData['age']){
        validMaxLen($age,'age',3);
        if(empty($err_msg['age'])){
            validNumber($age,'age');
        }
    }
    
    if($job !== $dbUserData['job']){
        validMaxLen($job,'job');
    }
    

    // 画像の処理
    if(!empty($_FILES['pic']['name'])){
        debug('画像処理のif文のループに入りました');
        $pic = uploadImg($_FILES['pic'],'pic');
    }else{
        $pic = '';
    }
    

    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        debug('update文を実行します');
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET email=:email, name=:name, age=:age,
            job=:job,pic=:pic WHERE id=:u_id AND delete_flg=0';
            $data = array(':email'=>$email,':name'=>$name,':age'=>$age,
            ':job'=>$job,':pic'=>$pic, ':u_id'=>$_SESSION['user_id']);

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
$siteTitle = 'プロフィール編集';
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
                <p>メールアドレスとパスワードをご入力いただき、「会員登録する」ボタンを押してください</p>
            </div>
            <form action="" method="post" class="input-form" enctype="multipart/form-data">
                <div class="area-msg common-msg">
                    <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
                </div>

                <div class="input-area">
                    <span class="form-tag">必須</span>
                    <label>メールアドレス</label>
                    <div class="input-section">
                        <input type="text"  class="<?php echo getErr('email');?>" name="email" value="<?php echo getFormData('email');?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['email'])) echo $err_msg['email'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area">
                    <span class="form-tag free-tag">任意</span>
                    <label>ニックネーム</label>
                    <div class="input-section">
                        <input type="text" class="<?php echo getErr('name');?>" name="name" value="<?php echo getFormData('name');?>">
                        <div class="area-msg err-msg">
                            <?php if(!empty($err_msg['name'])) echo $err_msg['name'];?>
                        </div>
                    </div>
                </div>
                
                <div class="input-area">
                    <span class="form-tag free-tag">任意</span>
                <label>年齢</label>
                <div class="input-section err-msg">
                    <input type="text" class="<?php echo getErr('age');?>" name="age" value="<?php echo getFormData('age');?>">
                    <div class="area-msg">
                        <?php if(!empty($err_msg['age'])) echo $err_msg['age'];?>
                    </div>
                </div>
                </div>

                <div class="input-area">
                    <span class="form-tag free-tag">任意</span>
                <label>職業</label>
                <div class="input-section err-msg">
                    <input type="text" class="<?php echo getErr('job');?>" name="job" value="<?php echo getFormData('job');?>">
                    <div class="area-msg">
                        <?php if(!empty($err_msg['job'])) echo $err_msg['job'];?>
                    </div>
                </div>
                </div>      

                <div class="input-area prof-pic-area">
                    <span class="form-tag free-tag">任意</span>
                <label>プロフィール画像</label>
                <div class="input-pic">
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" class="<?php echo getErr('pic');?> 
                    prev-img" name="pic"
                    value="<?php if(!empty($_POST['pic'])) echo $_POST['pic'];?>">
                    <img src="<?php echo getFormData('pic');?>" alt="" class="prev-img2">
                    <span style="<?php if(!empty(getFormData('pic'))) echo 'display:none;'?>" >ドラッグ&ドロップ</span>
                    <div class="area-msg">
                        <?php if(!empty($err_msg['pic'])) echo $err_msg['pic'];?>
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


