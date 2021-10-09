<?php

require('function.php');

debug('画像表示HTMLの練習');








?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="reset.cssd">
    <link rel="stylesheet" href="style2.css">
    <title>Document</title>
</head>
<body>
    <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err';?>" style="height:370px;line-height:370px;">
        <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
        <input type="file" name="pic" class="input-file" style="height:370px">
        <img src="" alt="" class="prev-img">
            ドラッグ&ドロップ
    </label>



</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    //画像のライブプレビュー
    // 1.ホバー、ドラッグされた時の表現
    // 2.ファイルが変化したときに、そのファイルを取得する
    // 3.imgタグのsrc属性に、文字情報に変換した画像データを設定する

    $(function(){
        console.log('Hello jquery');
        var $dropArea = $('.area-drop');
        var $fileInput = $('.input-file');

        $dropArea.on('dragover',function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border','3px #ccc dashed');
        });

        $dropArea.on('dragleave',function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).css('border','none');
        });

        $fileInput.on('change',function(e){
            $dropArea.css('border','none');
            var file = this.files[0],                  // 2.files配列にファイルが入っています
                $img = $(this).siblings('.prev-img');  // 3.jQueryのsiblingsメソッドで兄弟のimgを取得
                fileReader = new FileReader();         // 4.ファイルを読み込むFileReaderオブジェクト
            console.log(file);
            console.log(fileReader);
                // 5.読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット 
               
                fileReader.onload = function(event){
                    console.log('loadしました');
                    // 読み込んどデータをimgに設定
                    $img.attr('src',event.target.result).show();
                };
            
                // 6.画像の読み込み
                fileReader.readAsDataURL(file);

        });
        

    });


</script>

</html>

