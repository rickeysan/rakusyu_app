<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' プロフィール編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// ログイン認証
require('auth.php');

$userProf = getUserProf($_SESSION['user_id']);
debug('$userProfの中身：'.print_r($userProf,true));

// プロフィール画像に問題あり（要修正）
// 更新しても、プロフ画像は変わらない。もう一度、リダイレクトすると変わる





// プロフィール編集画面のロジック
// 1.post送信があるか判定
// 2.バリデーションチェック
// 3.usersテーブルにupdate文を実行
// 4.ヘッダーメッセージを設定

if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));
    debug('$_FILESの中身：'.print_r($_FILES,true));

    
    $email = $_POST['email'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $job = $_POST['job'];
    $pic = (!empty($_FILES)) ? uploadImg($_FILES['pic'],'pic') : '';
    
    

    // debug('ageの型1：'.gettype($age));
    // debug('ageの型1：'.gettype($userProf['age']));
    debug('picの値：'.$pic);

    // バリデーションチェック
    validRequire($email,'email');
    if(empty($err_msg) && $email !== $userProf['email']){
        validMaxLen($email,'email');
        validEmail($email,'email');
        if(empty($err_msg)){
            validEmailDup($email);
        }
    }
    if(!empty($name) && $name !== $userProf['name']){
        validMaxLen($name,'name');
    }
    if(!empty($age) && $age !== $userProf['age']){
        validMaxLen($age,'age',$len=3);
        validNumber($age,'age');
    }
    if(!empty($job) && $job !== $userProf['job']){
        validMaxLen($job,'job');
    }

    if(empty($pic) && !empty($userProf['pic'])){
        $pic = $userProf['pic'];
    }
    debug('pic2の値：'.$pic);

    debug('$err_msgの中身1：'.print_r($err_msg,true));

    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        try{
            $dbh = dbConnect();
            $sql = 'UPDATE users SET email=:email,name=:name,
            age=:age,job=:job,pic=:pic WHERE id=:u_id AND delete_flg=0';
            $data = array(':email'=>$email,':name'=>$name,':age'=>$age,
            ':job'=>$job,':pic'=>$pic,':u_id'=>$_SESSION['user_id']);
            $stmt = queryPost($dbh,$sql,$data);

            $result = $stmt->rowCount();
            debug('$resultの値：'.$result);
            if(!empty($result)){
                $_SESSION['suc_msg'] = SUC06;
                header("Location:mypage.php");
                exit();
                
            }else{
                $err_msg['common'] = MSG07;
            }
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }


debug('$err_msgの中身2：'.print_r($err_msg,true));
}




?>


<?php
$title="プロフィール編集";
require('head.php');
?>
    <!-- 操作完了後のメッセージスライド -->
    <div class="js-msg-slide">
        <p>
            <?php echo getSlideMsg();?>
        </p>
    </div>

    <?php require('header.php');?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">プロフィール編集</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">(1) 達成したい目標を設定する<br>
                    (2) 最も障害となりそうな要因を考える<br>
                    (3) その障害に直面したとき、どうするか考える</p>
            </div>

            <div class="col2-wrap">
                <div class="col2-section-wrap">
                    <form action="" method="post" enctype="multipart/form-data">

                        <div class="section-title">Myプロフィール</div>
                        
                        <div class="col2-habit-post-card">

                            <div class="text-field-wrap">
                                <div class="text-field-upper">
                                    <span class="text-field-tag">必須</span>
                                    <label for="" class="prof-text-field-label">Email</label>
                                    <input class="prof-input" type="text" name="email"  value="<?php echo getFormData($userProf,'email');?>">
                                </div>
                                <div class="text-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email'];?></p>
                                </div>  
                            </div>
                            
                            <div class="text-field-wrap">
                                <div class="text-field-upper">
                                    <span class="text-field-tag">任意</span>
                                    <label for="" class="prof-text-field-label">名前</label>
                                    <input class="prof-input" type="text" name="name"  value="<?php echo getFormData($userProf,'name');?>">
                                </div>
                                <div class="text-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['name'])) echo $err_msg['name'];?></p>
                                </div>  
                            </div>

                            <div class="text-field-wrap">
                                <div class="text-field-upper">
                                    <span class="text-field-tag">任意</span>
                                    <label for="" class="prof-text-field-label">年齢</label>
                                    <input class="prof-input" type="number" name="age"  value="<?php echo getFormData($userProf,'age');?>">
                                </div>
                                <div class="text-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['age'])) echo $err_msg['age'];?></p>
                                </div>  
                            </div>

                            <div class="text-field-wrap">
                                <div class="text-field-upper">
                                    <span class="text-field-tag">任意</span>
                                    <label for="" class="prof-text-field-label">職業</label>
                                    <input class="prof-input" type="text" name="job"  value="<?php echo getFormData($userProf,'job');?>">
                                </div>
                                <div class="text-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['job'])) echo $err_msg['job'];?></p>
                                </div>  
                            </div>

                            <div class="text-field-wrap pic-field-wrap">
                                <span class="text-field-tag">任意</span>
                                <label for="" class="prof-pic-field-label">プロフィール画像</label>
                                
                                <div for="" class="area-drop">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                    <img src="<?php echo getFormData($userProf,'pic');?>" style="<?php if(empty(getFormData($userProf,'pic'))) echo 'display:none';?>" alt="" class="prev-img">
                                    <input type="file" name="pic" class="input-file">
                                    ドラッグ＆ドロップ
                                </div>

                                <div class="text-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['pic'])) echo $err_msg['pic'];?></p>
                                </div>  
                            </div>
                            
                            <div class="habit-button-group">
                                <input class="habit-form-button" type="submit" value="編集する">
                            </div>
                        </div>
                        
                        
                        
                    </form>
                </div>
                    
                <div class="col2-sidebar-wrap">

                    <?php require('sidebar.php');?>
                        
                </div>


            </div>
                
        </div>


    </main>
<?php require('footer.php');?>
