<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 習慣登録・編集ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// ログイン判定
require('auth.php');

// カテゴリーデータをリストで取得
$category_list = getCategoryList();
debug('$category_listの中身'.print_r($category_list,true));

// 習慣登録・編集のロジック
// 登録の場合
// 1.post送信があるか判定
// 2.入力項目のバリデーションチェック
// 3.DBのhabitsテーブルにinsert文を実行
// 4.セッションにメッセージを格納
// 5.GETパラメータにh_idを付けて、遷移させる

// 新規登録か編集の判定(GETパラメータの判定)
$edit_flg = (isset($_GET['h_id'])) ? true : false;
debug('$edit_flgの値：'.$edit_flg);

$h_id = (isset($_GET['h_id'])) ? $_GET['h_id']:'';
debug('$h_idの値：'.$h_id);
$habit_data = getHabitData($h_id);
debug('$habit_dataの中身：'.print_r($habit_data,true));


// h_idがユーザーが登録したものか判定
if(!empty($h_id) && !isHabitUser($h_id,$_SESSION['user_id'])){
    debug('h_idに不適切な値が入りました');
    debug('マイページへ遷移します');
    header("Location:mypage.php");
}


if(!empty($_POST)){
    debug('post送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));

    // 変数に格納
    $habit = $_POST['habit'];
    $category = $_POST['category'];
    $obstacle = $_POST['obstacle'];
    $if_plan = $_POST['if_plan'];    
    // $open_flg = (isset($_POST['open'])) ? 1 : 0;
    
    if(isset($_POST['open'])){
        $open_flg = 1;
        debug('OK');
    }else{
        $open_flg = 0;
        debug('NG');
    }
    debug('$open_flgの値：'.$open_flg);

    // バリデーションチェック
    validRequire($habit,'habit');
    validRequire($category,'category');
    validRequire($obstacle,'obstacle');
    validRequire($if_plan,'if_plan');

    debug('$err_msgの中身1：'.print_r($err_msg,true));
    
    if(empty($err_msg)){
        validMaxLen($habit,'habit');
        validMaxLen($obstacle,'obstacle');
        validMaxLen($if_plan,'if_plan');
    }

    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        if($edit_flg){
            debug('編集です');
            try{
                $dbh = dbConnect();
                $sql = 'UPDATE habits SET habit=:habit,category_id=:c_id,
                obstacle=:obstacle, if_plan=:if_plan, open_flg=:open_flg
                WHERE id=:h_id AND delete_flg=0';
            $data = array(':habit'=>$habit,':c_id'=>$category,
            ':obstacle'=>$obstacle,':if_plan'=>$if_plan,
            ':open_flg'=>$open_flg, ':h_id'=>$h_id);
            debug('$dataの中身：'.print_r($data,true));
            
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->rowCount();
            debug('$resultの中身：'.print_r($result,true));
            if(!empty($result)){
                $_SESSION['suc_msg'] = SUC05;
                $url = 'makeHabit.php?h_id='.$_GET['h_id'];
                header("Location:".$url);
                return;
            }
        } catch (Exception $e){
            debug('ここです');
            error_log('エラーが発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }


        }else{
            debug('新規登録です');
            try{
                $dbh = dbConnect();
                $sql = 'INSERT INTO habits (user_id,habit,category_id,obstacle,if_plan,
            open_flg,create_date) VALUES (:user_id,:habit,:category_id,:obstacle,
            :if_plan,:open_flg,:create_date)';
            $data = array(':user_id'=>$_SESSION['user_id'],
            ':habit'=>$habit,':category_id'=>$category,':obstacle'=>$obstacle,
            ':if_plan'=>$if_plan,':open_flg'=>$open_flg,
            ':create_date'=>date('Y-m-d H:i:s'));
            debug('$dataの中身：'.print_r($data,true));
            
            $stmt = queryPost($dbh,$sql,$data);
            $result = $stmt->rowCount();
            debug('$resultの中身：'.print_r($result,true));
            if(!empty($result)){
                $_SESSION['suc_msg'] = SUC04;
                $url = 'makeHabit.php?h_id='.$dbh->lastInsertId();
                header("Location:".$url);
                return;
            }
        } catch (Exception $e){
            debug('ここです');
            error_log('エラーが発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
    }


debug('$err_msgの中身2：'.print_r($err_msg,true));

}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 操作完了後のメッセージスライド -->
    <div class="js-msg-slide">
        <p>
            <?php echo getSlideMsg();?>
            <?php debug('表示終了.$_SESSIONの中身：'.print_r($_SESSION,true));?>
        </p>
    </div>
    <?php require('header.php');?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title"><?php echo ($edit_flg) ? '習慣を編集する':'習慣を新規作成する' ?></h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">(1) 達成したい目標を設定する<br>
                    (2) 最も障害となりそうな要因を考える<br>
                    (3) その障害に直面したとき、どうするか考える</p>
            </div>

            <div class="col2-wrap">
                <div class="col2-section-wrap">
                    <form action="" method="post">

                        <div class="section-title">習慣を登録する</div>
                        
                        <div class="col2-habit-post">
                            <div class="common-err-info">
                                <p><?php if(!empty($err_msg['common'])) echo $err_msg['common'];?></p>
                            </div>
                            <div class="habit-wrap">
                                <div class="habit-row-item habit-row1-item">
                                    <span class="habit-item-title">達成したい習慣</span>
                                    <textarea name="habit" class="habit-item-form <?php echo getErr('habit');?>"><?php echo getFormData($habit_data,'habit');?></textarea>
                                </div>
                                <div class="habit-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['habit'])) echo $err_msg['habit'];?></p>
                                </div>  
                            </div>
                            
                            <div class="habit-wrap">
                                <div class="habit-row-item habit-row1-item">
                                    <span class="habit-item-title">習慣の<br>カテゴリー</span>
                                    <div class="habit-category-select-wrap">    
                                        <select class="habit-category-select" name="category" id="">
                                            <option value="" class="<?php echo getErr('category');?>">選択してください</option>
                                            <?php foreach($category_list as $key=>$val):?>
                                                <option value="<?php echo $val['id'];?>" <?php if($val['id'] == getFormData($habit_data,'category_id')) echo 'selected';?>><?php echo $val['name'];?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="habit-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['category'])) echo $err_msg['category'];?></p>
                                </div>  
                            </div>

                            <div class="habit-wrap">
                                <div class="habit-row-item habit-row2-item">
                                    <span class="habit-item-title">想定される障害</span>
                                    <textarea name="obstacle" class="habit-item-form item-row2 <?php echo getErr('obstacle');?>"><?php echo getFormData($habit_data,'obstacle');?></textarea>
                                </div>
                                <div class="habit-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['obstacle'])) echo $err_msg['obstacle'];?></p>
                                </div>
                            </div>
                                
                            <div class="habit-wrap">
                                <div class="habit-row-item habit-row2-item">
                                    <span class="habit-item-title">If-Then<br>ルール</span>
                                    <textarea name="if_plan" class="habit-item-form item-row2 <?php echo getErr('if_plan');?>"><?php echo getFormData($habit_data,'if_plan');?></textarea>
                                </div>
                                <div class="habit-field-footer">
                                    <p class="text-field-msg"><?php if(!empty($err_msg['if_plan'])) echo $err_msg['if_plan'];?></p>
                                </div>
                            </div>


                            <div class="open-button-wrap">
                                <input type="checkbox" name="open" <?php if(!empty($habit_data['open_flg'])) echo 'checked';?>>
                                <span>一般公開する</span>
                            </div>
                            
                            <div class="habit-button-group">
                                <input class="habit-form-button" type="submit" value="<?php echo ($edit_flg) ? '編集する' : '新規登録する';?>">
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

</body>
</html>