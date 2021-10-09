<?php

require('function.php');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 習慣の記録と進捗状況の確認ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// ログイン判定
require('auth.php');

// 習慣の表示ロジック
// 1.GETパラメータから、習慣のidを取得
// 2.$_SESSION['user_id']から、習慣のidがユーザーのものか判定
// 3.ユーザーのものであれば、記録更新のフォームを出す（違ったら、フォームを出さない）
// 4.

// Ajaxを利用して、日付を入力した時点で、その日の記録があるかないかを画面に表示する
// お気に入り機能を付けるためにAjaxの利用を考えていたが、上の機能を実装するためにAjaxを使う


$h_id = $_GET['h_id'];
$_SESSION['h_id'] = $h_id;

// DBからページに表示するのに必要な情報を取得する
// 習慣の基本データを取得

try{
    debug('表示する習慣の基本データをDBから取得します');
    $dbh = dbConnect();
    $sql = 'SELECT h.id,h.habit,h.obstacle,h.if_plan,c.name AS category_name 
    FROM habits AS h LEFT JOIN category AS c ON h.category_id =c.id 
    WHERE h.id=:h_id AND h.delete_flg=0';

    // $sql ='SELECT id,habit,obstacle,if_plan FROM habits
    // WHERE user_id=:u_id AND id=:h_id AND delete_flg=0';
    $data = array(':h_id'=>$h_id);
    debug('SQLに入れるデータ'.print_r($data,true));
    $stmt = queryPost($dbh,$sql,$data);
    $userHabit = $stmt->fetch(PDO::FETCH_ASSOC);
    

    debug('取得した習慣：'.print_r($userHabit,true));

} catch (Exception $e){
    error_log('エラーが発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
}



// 習慣の進歩状況のデータ

try{
    debug('表示する習慣のデータをDBから取得します');
    $dbh = dbConnect();
    $sql = 'SELECT h.id,h.habit,h.obstacle,h.if_plan,r.achievement,
    r.comment,r.date FROM habits AS h LEFT JOIN record AS r
    ON h.id=r.habit_id WHERE h.id=:h_id AND h.delete_flg=0 AND r.delete_flg=0
    ORDER BY r.date DESC';

    $data = array(':h_id'=>$h_id);
    debug('SQLに入れるデータ'.print_r($data,true));
    $stmt = queryPost($dbh,$sql,$data);
    $userHabitLog = $stmt->fetchAll();

    debug('取得した習慣の進歩状況：'.print_r($userHabitLog,true));

} catch (Exception $e){
    error_log('エラーが発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
}

$record_today = false;




// ログ更新

try{
    $dbh = dbConnect();
    $sql = 'SELECT id FROM habits WHERE id=:h_id AND user_id=:u_id AND 
    delete_flg=0';
    $data = array(':h_id'=>$h_id,':u_id'=>$_SESSION['user_id']);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->rowCount();
    if(!empty($result)){
        debug('この習慣はユーザーが登録したものです');
        $user_habit_flg = true;
    }else{
        debug('この習慣はユーザーが登録したものではありません');
        $user_habit_flg = false;
    }
} catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
}



// 習慣の記録ロジック
// 1.post送信があるか確認
// 2.バリデーションチェック（日付と達成度は必須、コメントは任意）
// 3.DBのrecordテーブルにinsert文を実行する
// 4.同じ日の入力をどうするか？→更新は一日に一回のみにする
// 5.ボタンを押せないようにする


if($user_habit_flg && !empty($_POST)){
    debug('post送信があります');
    debug('$_POSTの中身：'.print_r($_POST,true));
    $record_date = $_POST['record_date'];
    if(!isset($_POST['record_achieve'])){
        $err_msg['record_achieve'] = MSG01;
    }else{
        $record_achieve = $_POST['record_achieve'];
    }
    $record_comment = $_POST['record_comment'];

    // バリデーションチェック
    validRequire($record_date,'record_date');
    
    if(empty($err_msg)){
        debug('バリデーションチェックOKです');
        try{
            $dbh = dbConnect();
            $sql = 'INSERT INTO record 
             (habit_id,achievement,comment,date,create_date,delete_flg)
             VALUES (:h_id,:achievement,:comment,:date,:create_date,:delete_flg)';
            $data = array(':h_id'=>$h_id,':achievement'=>$record_achieve,
            ':comment'=>$record_comment,':date'=>$record_date,
            ':create_date'=>date('Y-m-d H:i:s'),':delete_flg'=>0);
            $stmt = queryPost($dbh,$sql,$data);
            if($stmt){
                $_SESSION['suc_msg'] = SUC07;
                $url = 'checkAndLogHabit.php'.appendgetParam();
                header("Location:".$url);
                exit();
            }
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }



    debug('$err_msgの中身：'.print_r($err_msg,true));
}


// 特定の日付の進歩状況がすでに存在するかを判定する処理フロー
// 1.input[type="date"]の値を取得
// 2.日付をAjaxでDBのrecordテーブルで検索
// 3.データがあれば、その旨を画面に表示して、ボタンを押せなくする


// 記録カレンダーの表示ロジック
// 1.DBのhabitテーブルとrecordテーブルでon h.id=r.habit_idで結合して、select文を実行
// 2.カレンダー生成プログラムに、〇△×を加える
// 3.HTML上に表示する


debug('テスト-------------------------');
$ym = date('Y-m');
$timestamp = strtotime($ym,'-01');
$today = date('Y-m-d');

$html_title = date('Y年n月', $timestamp);

// 当該付きの日数を取得
$day_count = date('t',$timestamp);
// 1日が何曜日か
$youbi = date('w',$timestamp);
// カレンダー作成の準備
$weeks = [];
$week = '';

// 第1週目：空のセルを追加
$week .= str_repeat('<td></td>',$youbi);
debug('カレンダーを生成します');

for($day = 1; $day <=$day_count;$day++,$youbi++){
    $calender_flg = false;
    if(1<= $day && $day <= 9){
        $compare_date = date('Y-m-0').$day;
    }else{
        $compare_date = date('Y-m-').$day;
    }
    // 進歩状況のデータがあるか判定
    foreach($userHabitLog as $key=>$val){
        if($val['date'] == $compare_date){
            $calender_achievement = $val['achievement'];
            $calender_flg = true;
        }
    }

    if($calender_flg){
        $week .='<td>'.$day;
        if($calender_achievement == 0){
            $week .= '<span class="material-icons">radio_button_unchecked</span>';
        }elseif($calender_achievement == 1){
            $week .= '<span class="material-icons">change_history</span>';
        }else{
            $week .= '<span class="material-icons">close</span>';
        }
        $week .= '</td>';

    }else{
        // debug($day.'日の更新');
        $week .='<td>'.$day.'</td>';
        $calender_flg = false;
    }

    // 週終わり、または、月終わりの場合
    if($youbi % 7 ==6 || $day == $day_count){
        if($day == $day_count){
            // 月の最終日の場合、空セルを追加
            // 月の最終日が火曜日2,9,16,23,30の場合、水・木・金・土分の4つの空セルを追加
            $week .=str_repeat('<td></td>',(6-$youbi%7));
        }else{
        }
        $weeks[] = '<tr>'.$week.'</tr>';
        $week = '';
    }
    
}
?>

<?php
$title="習慣の記録と進捗状況の確認ページ";
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
            <h1 class="main-title">習慣の記録と進歩確認</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">(1) 今日の記録を付けましょう<br>
                    (2) 達成できなかった場合、その原因をコメントで残しましょう<br>
                    (3) コメントを見直して、If-Thenルールを見直し・追加してみましょう</p>
            </div>

            <div class="col2-wrap">
                <div class="col2-section-wrap">
                    <?php if($user_habit_flg){ ;?>
                    <div class="section-title">習慣の記録</div>
                    <div class="archive-form">
                        <form method="post" action="checkAndLogHabit.php<?php echo appendGetParam();?>">
                        <p class="archive-form-info">
                            <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?></p>
                            <div>
                                <span class="archive-date">必須：日付</span><label for="">
                                    <input name="record_date" class="input-record" type="date">
                                    <p class="complete-form-err"><?php if(!empty($err_msg['record_date'])) echo $err_msg['record_date'];?></p>
                                </label>
                            </div>
                            <div class="archive-item-wrap">
                                <span class="archive-item-tag">必須：達成度</span>
                                <div class="archive-complete-form">
                                    
                                    <input type="radio" name="record_achieve" value="0">
                                    <span class="material-icons">
                                        radio_button_unchecked
                                    </span>
                                    <input type="radio" name="record_achieve" value="1">
                                    <span class="material-icons">
                                        change_history
                                    </span>
                                    <input type="radio" name="record_achieve" value="2">
                                    <span class="material-icons">
                                        close
                                    </span>
                                </div>
                                <p  class="complete-form-err">
                                    <?php if(!empty($err_msg['record_achieve'])) echo $err_msg['record_achieve'];?>
                                </p>
                            </div>
                                <span class="archive-comment-title">任意：コメント</span>
                                <textarea name="record_comment" id="" cols="30" rows="10"><?php if(!empty($_POST['record_comment'])) echo sanitize($_POST['record_comment']);?></textarea>
                                <input type="submit" value="記録する" class="archive-button" <?php if($record_today) echo 'disabled';?>>
                        </form>

                    </div>
                    <?php };?>



                        <div class="section-title">登録された習慣</div>
                        
                        <div class="col2-habit-post col2-habit-post-border">
                            <div class="habit-row-item habit-row1-item">
                                <span class="habit-item-title">達成したい習慣</span>
                                <p class="habit-item-content"><?php echo $userHabit['habit'];?></p>
                            </div>

                            <div class="habit-row-item habit-row1-item">
                                <span class="habit-item-title">習慣の<br>カテゴリ</span>
                                <p class="habit-item-content"><?php echo $userHabit['category_name'] ;?></p>
                            </div>
                            
                            <div class="habit-row-item habit-row2-item">
                                <span class="habit-item-title">想定される障害</span>
                                <p class="habit-item-content item-row2"><?php echo $userHabit['obstacle'];?></p>
                            </div>
                            
                            <div class="habit-row-item habit-row2-item">
                                <span class="habit-item-title">If-Then<br>ルール</span>
                                <p class="habit-item-content item-row2"><?php echo $userHabit['if_plan'];?></p>
                            </div>

                        </div>
                        
                        <div class="section-title">達成記録カレンダー</div>
                        
                        <div class="col2-habit-post col2-habit-post-border">
                        <h2 class="record-calender-title">
                            <?php echo date('n');?>月の進捗状況
                        </h2>

                        <table class="record-calender table-bordered">
                            <tbody>
                                <tr>
                                    <th>日</th>
                                    <th>月</th>
                                    <th>火</th>
                                    <th>水</th>
                                    <th>木</th>
                                    <th>金</th>
                                    <th>土</th>
                                </tr>

                            <?php
                                foreach($weeks as $week){
                                    echo $week;
                                }
                            ?>

                            </tbody>

                        </table>

                        </div>

                        <div class="section-title">コメント一覧</div>
                        
                        
                        <div class="col2-habit-post col2-habit-post-border">
                            <?php if(!empty($userHabitLog)){ ?>
                                <?php foreach($userHabitLog as $key=>$val):?>
                                <div class="habit-row-item habit-row2-item">
                                    <span class="habit-item-title"><?php echo $val['date'];?></span>
                                    <p class="habit-item-content"><?php echo $val['comment'];?></p>
                                </div>
                                <?php endforeach;?>
                            <?php } else {
                                echo 'コメントはありません'; }
                            ?>
                            
                        </div>

                        
                        
                </div>
                    
                <div class="col2-sidebar-wrap">

                    <sidebar class="col2-sidebar">
                        <?php require('sidebar.php');?>

                    </sidebar>
                        
                </div>




            </div>
                
        </div>


    </main>
<?php require('footer.php');?>
