<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' Ajax');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

// Ajax処理

if(!empty($_POST['date'])){
    debug('post送信があります');
    $date = $_POST['date'];
    debug('$dateの値：'.$date);
    try{
        $dbh = dbConnect();
        $sql = 'SELECT id FROM record WHERE habit_id=:h_id AND 
        date=:h_date AND delete_flg=0';
        $data = array(':h_id'=>$_SESSION['h_id'],':h_date'=>$date);
        $stmt = queryPost($dbh,$sql,$data);
        $result =$stmt->rowCount();
        if(!empty($result)){
            debug('その日付のデータはすでにあります');
            $archieve_form_flg = true;
        }else{
            debug('その日付のデータはまだありません');
            $archieve_form_flg = false;
        }

    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }

}

?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(function(){
        var $archive_info = $('.archive-form-info');
        $archive_info.text('さようなら');


        <?php
        if($archieve_form_flg){
            debug('日付データありefdsce');
        ?>
        // $archive_info.text('その日はすでに記録済です')
        $archive_info.text('こんにちは');

        <?php }else{
            debug('日付データあり');
        ?>
        $archive_info.text('')

        <?php } ?>

    })


    


</script>

