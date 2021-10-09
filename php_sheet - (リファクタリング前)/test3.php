<?php

require('function.php');
debug('テスト-------------------------');
$ym = date('Y-m');
debug($ym);
$timestamp = strtotime($ym,'-01');
debug($timestamp);

// $ym2 = '2021-09-01';
// debug($ym2);
// $timestamp2 = strtotime($ym2,'-01');
// debug($timestamp2);

// 今日の日付　フォーマット　2021-09-10
$today = date('Y-m-d');
debug('$todayの中身：'.$today);

// カレンダーのタイトルを削減 2021年9月
$html_title = date('Y年n月', $timestamp);

// 当該付きの日数を取得
$day_count = date('t',$timestamp);
debug('day_count'.$day_count);
// 1日が何曜日か
$youbi = date('w',$timestamp);
debug('$youbi'.$youbi);
// カレンダー作成の準備
$weeks = [];
$week = '';

// 第1週目：空のセルを追加
$week .= str_repeat('<td></td>',$youbi);

for($day = 1; $day <=$day_count;$day++,$youbi++){
    debug($day.'日の更新');
    $week .='<td>'.$day.'</td>';

    // 週終わり、または、月終わりの場合
    if($youbi % 7 ==6 || $day == $day_count){
        if($day == $day_count){
            // 月の最終日の場合、空セルを追加
            // 月の最終日が火曜日2,9,16,23,30の場合、水・木・金・土分の4つの空セルを追加
            debug('6-$youbi%7'.(6-$youbi%7));
            $week .=str_repeat('<td></td>',(6-$youbi%7));
            debug('月終わりです');
        }else{
        debug('週終わりです');
        }
        $weeks[] = '<tr>'.$week.'</tr>';
        debug('週ごとの$weekの中身：'.$week);
        $week = '';
    }
    
}

debug('$weeksの中身：'.print_r($weeks));
var_dump($weeks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カレンダ</title>
    <style>
        .record-table{
            width: 400px;
            height: 300px;
        }
        td{
            text-align: center;
            width: 60px;
            height: 50px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <h2>7月の進捗状況</h2>
    <table class="record-table table-bordered mb-5">
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


</body>
</html>