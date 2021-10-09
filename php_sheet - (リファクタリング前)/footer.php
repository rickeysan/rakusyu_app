<footer class="footer">あいうえお</footer>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function(){
    console.log('こんにちは');    


    // メッセージスライドの処理
    // DOMを取得する
    // msg-slideにメッセージがあるかどうかを判断
    // メッセージがあれば、トグルで出現させる。その５秒後に、ひっこめる
    var $jsMsgSlide = $('.js-msg-slide');
    var msg = $jsMsgSlide.text();
    if($sample=msg.replace(/^[\s　]+|[\s　]+$/g,'').length){
        $jsMsgSlide.slideToggle('slow');
        setTimeout(function(){
        $jsMsgSlide.slideToggle('slow');
        },5000);
    }

    // 画像のライブプレビュー
    // 1.area-dropにドラッグ、ドラッグリーブされたら、枠線を付ける、外す
    // 2.input-fileのDOMオブジェクトがchangeしたら、

    // オブジェクトを生成
    var $areaDrop = $('.area-drop');
    var $inputFile = $('.input-file');

    $areaDrop.on('dragover',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','3px #ccc dashed');
    });

    $areaDrop.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','none');
    });

    $inputFile.on('change',function(e){
        $areaDrop.css('border','none');
        var file = this.files[0],
            $img = $(this).siblings('.prev-img'),
            fileReader = new FileReader();

            fileReader.onload = function(event){
                $img.attr('src',event.target.result).show();
            }

            fileReader.readAsDataURL(file);

    });

    // 特定の日付にデータがあるか判定
    var $input_date = $('.input-record');
    console.log($input_date);
    $input_date.on('change',function(){
        console.log('チェンジされました');
        var $date = this.value;
        console.log($date);

        $.ajax({
            type:"POST",
            url:"ajaxDate.php",
            data:{date : $date}
        }).done(function(){
            console.log('Ajax Success');
        }).fail(function(){
            console.log('Ajax Error');
        });
    });

    
})

    </script>
