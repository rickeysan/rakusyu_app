
<footer id="footer">All Rights Reserved.</footer>


<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
$(function(){
    console.log('こんにちは');
    // フッターを最下部に固定
    var $ftr = $('#footer');
    console.log(window);
    console.log($ftr);
    console.log(window.innerHeight);
    console.log($ftr.offset());
    console.log($ftr.offset().top);
    console.log($ftr.innerHeight());
    console.log($ftr.outerHeight());

    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
        console.log('footer位置を調整します');
        $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }


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
    // 1. inputの値が変化したか判定
    // 2. inputの値（日付）を取得
    // 3. ajaxDate.phpに送信
    // 4. dataから、true or falseを取得
    // 5. 適当なクラスをつける、外す、メッセージを表示する、非表示にする
    var $record_date = $('.js-input-record-date');
    console.log($record_date);
    $record_date.on('change',function(){
        console.log('日付が変わりました');
        $that = $(this);

        $.ajax({
            type:'post',
            url:'ajaxDate.php',
            dataType:'json',
            data:{
                date:$that.val(),
            }
        }).then(function(data){
            
            console.log(data);

            if(data.dateFlg){
                $that.addClass('input-record-is-error');
                $that.removeClass('input-record-is-success');
                $('.record-date-msg').html('その日付はすでに記録済みです');
                $('.js-archive-button').prop('disabled',true);
            }else{
                $that.addClass('input-record-is-success');
                $that.removeClass('input-record-is-error');
                $('.record-date-msg').html('');
                $('.js-archive-button').prop('disabled',false);
            }


        })
    });

var slider = (function(){
    var $currentItemNum = 1;
    var $slideContainer = $('.slider__container');
    var slideItemNum = $('.slider__item').length;
    var slideItemWidth = $('.slider__item').innerWidth();
    var slideContainerWidth = slideItemWidth * slideItemNum;
    var DURATION = 500;

    return {
        slidePrev: function(){
            if($currentItemNum > 1){
                $slideContainer.animate({left:'+='+slideItemWidth+'px'},DURATION);
                $currentItemNum--;
            }
        },
        slideNext: function(){
            if($currentItemNum < slideItemNum){
                $slideContainer.animate({left:'-='+slideItemWidth+'px'},DURATION);
                $currentItemNum++;
            }
        },
        init: function(){
            $slideContainer.attr('style','width;'+slideContainerWidth+'px');
            var that = this;
            $('.js-slide-next').on('click',function(){
                that.slideNext();
                console.log($currentItemNum);
            });
            $('.js-slide-prev').on('click',function(){
                that.slidePrev();
                console.log($currentItemNum);

            })
        }
    }
})();
slider.init();

});

    </script>

</body>
</html>