<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' マイページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');


// ログイン認証
require('auth.php');

// マイページのロジック（My習慣の一覧を表示する）
// 1.user_idをセッション変数から取得する
// 2.DBのhabitsテーブルでselect文を実行する
// 3.HTMLに表示する

try{
    $dbh = dbConnect();
    $sql ='SELECT h.id,h.habit,h.obstacle,h.if_plan,c.name AS category_name FROM habits
    AS h LEFT JOIN category AS c ON h.category_id = c.id
    WHERE user_id=:u_id AND h.delete_flg=0';
    $data = array(':u_id'=>$_SESSION['user_id']);
    
    $stmt = queryPost($dbh,$sql,$data);
    $userHabitsList = $stmt->fetchAll();
    
    debug('My習慣一覧：'.print_r($userHabitsList,true));

} catch (Exception $e){
    error_log('エラーが発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
}









?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 操作完了後のメッセージスライド -->
    <div class="js-msg-slide">
        <p>
            <?php echo getSlideMsg();?>
            <?php debug('表示終了.$_SESSIONの中身：'.print_r($_SESSION,true));?>
            <!-- パスワードの変更が完了しました! -->
        </p>
    </div>
    
    <?php require('header.php');?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">マイページ</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">メールアドレスとパスワードをご入力いただき、
                    「ログイン」ボタンを押してください</p>
            </div>

            <div class="col2-wrap">
                <div class="col2-section-wrap">
                    <div class="section-title">MY Habit一覧</div>

                    <?php if(!empty($userHabitsList)):
                        foreach($userHabitsList as $key=>$val):
                        debug('$keyの値：'.$key);
                        debug('$valの値：'.print_r($val,true));
                    
                    ?>
                    <div class="col2-habit-post">
                        <div class="habit-row-item habit-row1-item">
                            <span class="habit-item-title">達成したい習慣</span>
                            <p class="habit-item-content habit-row1-item"><?php echo $val['habit'] ;?></p>
                        </div>

                        <div class="habit-row-item habit-row1-item">
                            <span class="habit-item-title">習慣の<br>カテゴリ</span>
                            <p class="habit-item-content"><?php echo $val['category_name'] ;?></p>
                        </div>

                        <div class="habit-row-item habit-row2-item">
                            <span class="habit-item-title">想定される障害</span>
                            <p class="habit-item-content item-row2"><?php echo $val['obstacle'] ;?></p>
                        </div>

                        <div class="habit-row-item habit-row2-item">
                            <span class="habit-item-title">If-Then<br>ルール</span>
                            <p class="habit-item-content item-row2"><?php echo $val['if_plan'] ;?></p>
                        </div>
                        <div class="habit-button-group">
                            <a href="makeHabit.php?h_id=<?php echo $val['id'];?>">編集する</a>
                            <a href="checkAndLogHabit.php?h_id=<?php echo $val['id'];?>">記録する</a>
                        </div>
                    </div>
                    
                    <?php endforeach;
                        endif;
                    ?>


                    
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
