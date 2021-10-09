<footer class="footer">
    © 2021Profile
</footer>

<?php debug('footerです'); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    // 画像ライブプレビュー処理
// 画像が選択されたときに発火
// imgのsrc属性を、pathに置き換える

$(function(){
    console.log('こんにちは');
    var $pic_area = $('.prev-img');
    var $pic_img = $('.prev-img2');
    $pic_area.on('dragover',function(e){
        console.log('ドラッグされた');  
        e.stopPropagation();
        e.preventDefault();
        $(".input-pic").css('border','3px #ccc dashed');
    });
    $pic_area.on('dragleave',function(e){
        e.stopPropagation();
        e.preventDefault();
        $(".input-pic").css('border','none');
    });
    $pic_area.on('change',function(e){
        console.log('changeになった');
        $(".input-pic").css('border','none');
        var file = this.files[0];
            fileReader = new FileReader();
        
            fileReader.onload = function(e){
                $pic_img.attr('src',e.target.result).show();
            };
        
            fileReader.readAsDataURL(file);

    })
        
        
        
});
    
    
</script>


</html>