<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' Ajax-日付チェック-');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// Ajax処理

if(!empty($_POST)){
    debug('$_POST送信の中身：'.print_r($_POST,true));
    debug('$_SESSIONの中身：'.print_r($_SESSION,true));

    $dbh = dbConnect();
    $sql = 'SELECT id FROM record WHERE date =:date AND habit_id=:h_id';
    $data = array(':date'=>$_POST['date'],':h_id'=>$_SESSION['h_id']);

    $stmt = queryPost($dbh,$sql,$data);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    debug('$resultの中身：'.print_r($result,true));

    if(empty($result)){
        debug('その日付のデータはまだ存在しません');
        echo json_encode(array(
            'dateFlg'=>false,
        ));
    }else{
        debug('その日付のデータはすでに存在します');
        echo json_encode(array(
            'dateFlg'=>true,
        ));
    }

    exit();


}
