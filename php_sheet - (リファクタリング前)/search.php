<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' 検索画面');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

debug('$_GETの値：'.print_r($_GET,true));

// ログイン認証
require('auth.php');


// カテゴリーデータをリストで取得
$category_list = getCategoryList();
debug('$category_listの中身'.print_r($category_list,true));

$category_list_for_habits = array();
foreach($category_list as $key=>$val){
    $category_list_for_habits[$val['id']] = $val['name'];
}
debug('$category_list_for_habitsの値：'.print_r($category_list_for_habits,true));

// 検索結果一覧表示のロジック
// 1.初期画面では、id順に表示する
// 2.ページングで、10件ずつ表示する
// 3.DBのhabitsテーブルとusersテーブルに対して、left joinでselect文を実行する
// 4.HTMLにforeachで表示する

// 検索機能
// 1.post送信があれば、SQLにORDER BYを付け加える
$time_sort = (!empty($_GET['time_sort'])) ? $_GET['time_sort'] : '';
debug('$time_sortの値：'.$time_sort);
$category_sort = (!empty($_GET['category'])) ? $_GET['category'] : '';
debug('$category_sortの値：'.$category_sort);

$currentPage = (!empty($_GET['p'])) ? (int)$_GET['p'] : 1;
if(empty($_GET['p'])){
    $_GET['p'] = 1;
}
// debug('現在のページ数:'.$currentPage);
// debug('現在のページ数の型；'.gettype($currentPage));
$sql_offset = ($currentPage-1)*10;
// debug('$sql_offsetの値：'.$sql_offset);
$habitsNum = getHabitsListNum($category_sort);
// debug('習慣の数:'.$habitsNum);


// OFFSET LIMIT句を使う前に、すべてのデータ数を把握する必要がある
try{
    $dbh = dbConnect();
    $sql = 'SELECT h.id,h.habit,h.category_id,h.obstacle,h.if_plan,u.name,u.pic
    FROM habits AS h LEFT JOIN users AS u ON h.user_id = u.id 
    WHERE h.open_flg = 1 AND h.delete_flg=0';
    if(!empty($category_sort)){
        debug('カテゴリーをSQLに加えます');
        $sql .=' AND category_id='.$category_sort;
    }
    

    if($time_sort){
        debug('時系列順序をSQLに加えます');
        if($time_sort==1){
            debug('降順');
            $sql.=' ORDER BY h.create_date DESC';
        }elseif($time_sort==2){
            debug('昇順');
            $sql.=' ORDER BY h.create_date ASC';
        }
    }
    $sql.=' LIMIT 10 OFFSET '.$sql_offset;
    debug('SQL:'.$sql);
    $data = array();
    $stmt = queryPost($dbh,$sql,$data);
    $searchHabitsList = $stmt->fetchAll();
    debug('検索結果の中身：'.print_r($searchHabitsList,true));
    $_GET['p']  = 1;
    
}catch (Exception $e){
    error_log('エラー発生：'.$e->getMessage());
}

// ページングのロジック
// 用意する変数
// 1.currentPage(現在のページ),2.pageColNum(表示件数),3.totalPage(総ページ数),
// 4.minPage(ページングバーの最小ページ),5.maxPage(ページングバーの最大ページ),6.pagingNum(ページングのリンク数)


  
$listSpan = 10;
$pageColNum = 5;
$totalPage = ceil($habitsNum/$listSpan);
debug('$totalPageの値：'.$totalPage);

debug('$currentaPageの値：'.$currentPage);

// GET_['p']にありえない数字に入った時に遷移,ex.abcや膨大な数
// 下は、小数点を除外できてない（時間があれば、改善する）
if(!preg_match('/^[0-9]+$/', $currentPage)){
    debug('URLの不正な値が入りました------------------------------');
    header ("Location:search1.php"); 
}







?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>検索画面</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require('header.php');?>

    <main id="main">
        <div class="main-header">
            <h1 class="main-title">検索</h1>
        </div>

        <div class="main-wrap">
            <div class="page-info-wrap">
                <p class="page-info">ここでは他の会員の方のIf-thenルールを確認することができます。
                    自分の習慣化に取り入れやすそうなものを探してみましょう</p>
            </div>

            <div class="col2-wrap">
                <div class="col2-sidebar-wrap">

                        <form method="get" class="search-bar">
                            <div class="search-bar-item-wrap">
                                <span class="search-bar-tag">カテゴリー</span>
                                <select name="category" id="">
                                    <option value="0">選択してください</option>
                                        <?php foreach($category_list as $key=>$val):?>
                                            <option value="<?php echo $val['id'];?>"  <?php if($category_sort==$val['id']) echo 'selected';?>><?php echo $val['name'];?></option>
                                        <?php endforeach;?>
                                </select>
                            </div>
                            <div class="search-bar-item-wrap">    
                                <span class="search-bar-tag">表示順</span>
                                <select name="time_sort" id="">
                                    <?php $sort_array = array();
                                        $sort_array['time_sort'] = $time_sort;
                                    ?>
                                    <option value="0" <?php if(empty(getFormData($sort_array,'time_sort',false))) echo 'selected';?>>選択してください</option>
                                    <option value="1" <?php if((getFormData($sort_array,'time_sort',false)) ==1) echo 'selected';?>>新しい</option>
                                    <option value="2" <?php if((getFormData($sort_array,'time_sort',false))==2) echo 'selected';?>>古い</option>
                                </select>
                            </div>

                            <div class="search-bar-item-wrap">   
                                <input type="submit" value="検索する"> 
                            </div>

                        </form>
                        
                        
                </div>

                <div class="col2-section-wrap">
                    <div class="section-title">検索結果一覧</div>

                    <div class="result-show">
                        <p class="result-text"><?php echo $habitsNum;?>件の習慣が見つかりました</p>
                        <span class="result-number"><?php echo ($currentPage-1)*10+1;?>-<?php echo ($currentPage-1)*10+count($searchHabitsList);?>件 / <?php echo $habitsNum;?>件</span>
                    </div>

                    <div class="section-title">検索結果</div>

                    <div class="col2-habit-post col2-habit-post-height">

                    
                    <?php if(!empty($searchHabitsList)):
                        foreach($searchHabitsList as $key=>$val):?>
                        <a href="checkAndLogHabit.php?h_id=<?php echo $val['id'];?>" class="habit-card">

                            <div class="habit-row-item habit-row1-item">
                                <span class="habit-item-title habit-item-name">名前：<?php echo $val['name'];?></span>
                                <span class="habit-item-title habit-item-name">カテゴリー：<?php echo $category_list_for_habits[$val['category_id']];?></span>
                                <img class="habit-user_pic" src="<?php echo getImg($val['pic']);?>">
                            </div>
                            
                            <div class="habit-row-item habit-row1-item">
                                <span class="habit-item-title">達成したい習慣</span>
                                <p class="habit-item-content"><?php echo $val['habit'];?></p>
                            </div>
                            
                            
                            <div class="habit-row-item habit-row1-item">
                                <span class="habit-item-title">想定される障害</span>
                                <p class="habit-item-content item-row1"><?php echo $val['obstacle'];?></p>
                            </div>
                            
                            <div class="habit-row-item habit-row1-item">
                                <span class="habit-item-title">If-Then<br>ルール</span>
                                <p class="habit-item-content item-row1"><?php echo $val['if_plan'];?></p>
                            </div>
                        </a>
                    <?php 
                            endforeach;
                        endif; 
                    ?>

       
                    </div>
                    
                    <div class="paging-bar-section">
                        <div class="paging-bar-wrap">
                            <ul class="paging-bar">
                            <?php 
                           

                            if($pageColNum > $totalPage){
                                debug('ループ1');
                                $minPageNum = 1;
                                $maxPageNum = $totalPage;
                                
                            }else{
                                if($currentPage ==1){
                                debug('ループ2');
                                    $minPageNum = 1;
                                    $maxPageNum = $pageColNum;
                                }elseif($currentPage ==2){
                                debug('ループ3');
                                    $minPageNum = 1;
                                    $maxPageNum = $pageColNum;
                                }elseif($currentPage == $totalPage-1){
                                debug('ループ4');
                                    $minPageNum = $totalPage-4;
                                    $maxPageNum = $totalPage;
                                }elseif($currentPage == $totalPage){
                                debug('ループ5');
                                    $minPageNum = $totalPage-4;
                                    $maxPageNum = $totalPage;
                                }else{
                                debug('ループ6');
                                    $minPageNum = $currentPage-2;
                                    $maxPageNum = $currentPage+2;
                                }
                            }
                            $pageing_info = array();
                            $pageing_info['total_page'] =$totalPage;
                            $pageing_info['currentPage'] =$currentPage;
                            $pageing_info['minPageNum'] =$minPageNum;
                            $pageing_info['maxPageNum'] =$maxPageNum;
                            
                            debug('$pageing_info&'.print_r($pageing_info,true));

                            ?>
                            <?php if($currentPage != 1):?>
                                <li class="paging-bar-item"><a href="search.php<?php echo (!empty($_GET['category']) || !empty($_GET['time_sort'])) ? appendGetParam(array('p')).'&p=1' : appendGetParam(array('p')).'?p=1'; ?>">&lt;</a></li>
                            <?php endif; ?>
                            <?php
                            for($i=$minPageNum; $i<=$maxPageNum;$i++):
                            ?>
                                <li class="paging-bar-item" <?php if($currentPage == $i) echo 'active';?>>
                                    <a href="search.php<?php debug('ページング:$_GETの中身：'.print_r($_GET,true)); echo (!empty($_GET['category']) || !empty($_GET['time_sort'])) ? appendGetParam(array('p')).'&p='.$i : appendGetParam(array('p')).'?p='.$i;?>">
                                        <?php echo $i;?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            <?php if($currentPage != $totalPage):?>
                                <li class="paging-bar-item"><a href="search.php<?php echo (!empty($_GET['category']) || !empty($_GET['time_sort'])) ? appendGetParam(array('p')).'&p='.$totalPage : appendGetParam(array('p')).'?p='.$totalPage?>">&gt;</a></li>
                            <?php endif; ?>

                            </ul>
                        </div>
                    </div>


                </div>

                




            </div>
                
        </div>


    </main>

</body>
</html>