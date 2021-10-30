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

    <div class="main-wrap main-wrap-slider-wrap">
        <div class="img-slider-arrow-wrap">
            <i class="fas js-slide-prev" aria-hidden="true"></i>
            <i class="fas js-slide-next"></i>
        </div>
        <ul class="main-img-slider">
            <li class="img-slider-item">
                <img class="" src="img/powerpoint_img/Slide1.jpg" alt="">
            </li>
            <li class="img-slider-item">
                <img class="" src="img/powerpoint_img/Slide2.jpg" alt="">
            </li>
            <li class="img-slider-item">
                <img class="" src="img/powerpoint_img/Slide3.jpg" alt="">
            </li>
            <li class="img-slider-item">
                <img class="" src="img/powerpoint_img/Slide4.jpg" alt="">
            </li>
            <li class="img-slider-item">
                <img class="" src="img/powerpoint_img/Slide5.jpg" alt="">
            </li>
        </ul>

    </div>










