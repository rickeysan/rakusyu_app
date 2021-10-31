<?php

// 共通変数と関数の読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「');
debug(' トップページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「');

?>

<?php
$title= 'トップページ';
require('head.php');
?>

<?php require('header.php');?>
<main id="main">
    <div class="main-wrap main-wrap-slider-wrap">
        <div class="slider">
            <i class="fas fa-angle-left slider__nav slider__prev js-slide-prev" aria-hidden="true"></i>
            <i class="fas fa-angle-right slider__nav slider__next js-slide-next" aria-hidden="true"></i>
            <ul class="slider__container">
                <li class="slider__item slidere__item1"><img src="img/powerpoint_img/Slide1.jpg"></li>
                <li class="slider__item slidere__item2"><img src="img/powerpoint_img/Slide2.jpg"></li>
                <li class="slider__item slidere__item3"><img src="img/powerpoint_img/Slide3.jpg"></li>
                <li class="slider__item slidere__item4"><img src="img/powerpoint_img/Slide4.jpg"></li>
                <li class="slider__item slidere__item5"><img src="img/powerpoint_img/Slide5.jpg"></li>
            </ul>
        </div>

    </div>


</main>
<?php require('footer.php');?>







