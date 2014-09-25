<?php defined('SYSPATH') or die('No direct script access.');
/*
 * php5shop
 * Список переменных для подстановки:
 *
 * title                                - заголовок страницы
 * logo                                 - блок для logo
 * keywords                             - meta keywords для поисковых систем
 * description                          - meta description для поисковых систем
 * css                                  - стили
 * topBlock1                            - верхний виджет 1 (выбор валюты)
 * topBlock2                            - верхний виджет 2 (корзина)
 * topBlock3                            - верхний виджет 3 (дополнительный) - сортировка в категории
 * topTitle                             - заголовок, название магазина (текст-html)
 * menu[]                               - верхнее меню
 * banner1                              - большой баннер в центре страницы
 * about                                - блок для приветствия посетителя
 * stuff                                - блок товаров
 * cats                                 - категории товаров
 * banner2                              - баннер под левым блоком
 * banner3                              - баннер над правым блоком
 * loginForm                            - блок с формой авторизации
 * lastNews                             - блок виджета новостей
 * banner4                              - место в footer для рекламы
 * url::base()                          - каталог от корня вирт. сервера, куда установлен магазин
 * prod1                                - товар для сравнения 1
 * prod2                                - товар для сравнения 2
 * oneProductPage                       - метка о том, что это страница одного товара (для микроразметки)
 * breadcrumbs                          - навигационная цепочка
 * themes                               - массив со списком доступных для применения шаблонов дизайна
 * theme                                - выбранный шаблон дизайна
 * special_pages                        - ссылки на дополнительные страницы
 */
?><!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language" content="ru">
    <base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base(); ?>">
    <meta name="copyright" content="<?php echo $_SERVER['HTTP_HOST']; ?>">
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="cmsmagazine" content="f36b4b17fe8e41ffb1bc9b164f77b732">
    <link rel="shortcut icon" type="image/ico" href="images/favicon.gif">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="rss.xml">
    <link rel="stylesheet" href="template1.css">
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="js/jquery.simplemodal.js"></script>
    <?php echo $css; ?>
    <?php if (isset($lastNews)): ?>
        <script type="text/javascript" src="js/jquery.slides.min.js"></script>
    <?php endif; ?>
<!--
php5shop - CMS интернет-магазина
Copyright (C) 2010-2014 phpdreamer, php5shop.com
-->

</head>

<body>
<div id="header">
    <a href="" class="float"><?php echo $logo; ?></a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="position:absolute;top:1px;left:1px;height:0px;width:0px;overflow:hidden"><h1><a href="http://phpdreamer.ru/" target="_blank">http://phpdreamer.ru/</a></h1> <h1><a href="http://php5shop.com">free php shop CMS</a></h1></div>
    <?php echo $loginForm; ?>

    <div class="blocks" style="float: right; ">
        <!-- Блок поиска -->
        <img src="images/top_bg.gif" alt="" width="218" height="12">

        <p style="margin-left:20px;">
            <input type="text" id="inputsearch" onkeydown="if(isEnterKey(this, event)) my_search();">
        </p>

        <p>&nbsp;<a href="javascript:void(0);" id="buttonsearch">Поиск</a></p>
        <img src="images/bot_bg.gif" alt="" width="218" height="10"><br>

        <div style="display:none" id="searchresults"></div>
        <script type="text/javascript">
            function isEnterKey(obj, event) {
                event = event || window.event;
                var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : null;
                if (keyCode == 13) {
                    return true;
                }
                return false;
            }
            $('#buttonsearch').click(function () {/*Поиск по продуктам (AJAX)*/
                my_search();
            });
            function my_search() {
                $.post('ajax/search/' + encodeURIComponent($('#inputsearch').val()), '[]', function (data) {
                    var str = '<' + 'h2' + '>' + 'Результаты поиска:<' + '/h2' + '><' + 'br' + '>';
                    if (data.length == 0)
                        str += 'Ничего не найдено<' + 'br' + '>';
                    else
                        for (var k in data)
                            str += '<' + 'a h' + 'ref="' + data[k].path + '">' + data[k].name + '<' + '/a' + '><' + 'br' + '>';
                    $('#searchresults').html(str);
                    $('#searchresults').modal();
                    $('#simplemodal-container').css('width', '400px');
                    $('#simplemodal-container').css('height', '350px');
                }, 'json');
            }
        </script>
        <!-- /Блок поиска -->
    </div>

    <div id="topTitle"><?php echo $topTitle; ?></div>


    <!-- Верхнее меню-->
    <ul id="menu">
        <?php if ($menu[1]): ?>
            <li><a href="">
                    <img style="margin: 5px 0px" src="images/home.png" alt="Витрина">
                </a>
            </li>
        <?php endif;
        if ($menu[2]): ?>
            <li><a href="blog">Новости</a></li>
        <?php endif;
        foreach ($special_pages as $page_uri => $page_label):
                    ?>
            <li><a href="<?php echo htmlspecialchars($page_uri); ?>"><?php echo htmlspecialchars($page_label); ?></a></li>

        <?php endforeach;
        if ($menu[3]): ?>
        <li><a href="admin/">Панель управления</a></li>
        <?php endif;
        if ($menu[4]): ?>
            <li><a href="shop/user">Личный кабинет</a></li>
        <?php endif;
        if ($menu[5]): ?>
            <li><a href="order/cart">Корзина</a></li>
        <?php endif;
        if ($menu[6]): ?>
            <li><a href="rss.xml">
                    <img style="margin: 5px 0px" src="images/rss_icon.png" alt="RSS">
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <!-- /меню-->
</div>
<script type="text/javascript">
    var add = "images/active/";
    var path = document.location.pathname;
    var path2 = "";
    $("ul#menu").find("a").each(function () {
        path2 = $(this).attr('href');
        if (path == "/" + path2 && path2) {
            $(this).parent().addClass('active');
        }
    });
</script>
<div id="container">

    <div id="center" class="column">
        <?php echo $banner1; ?>
        <div id="content">
            <div id="breadcrumbs"><?php echo $breadcrumbs; ?></div>
            <?php echo $about; ?>
            <div class="stuff" <?php
            if (isset($oneProductPage))
                echo 'itemscope itemtype="http://schema.org/Product"'; ?>>
                <?php echo @$stuff; ?>
            </div>
        </div>
    </div>
    <div id="left" class="column">

        <div id="navigation">
            <span id="cat-header">Категории товаров:</span>

            <div style="padding: 15px; padding-top: 0px;">
                <?php echo $cats; ?>
            </div>

        </div>
        <?php echo $banner2; ?>
    </div>
    <div id="right" class="column">

        <?php echo $banner3; ?>

        <div class="rightblock">

            <?php echo $topBlock2; ?>
            <?php echo @$topBlock1; ?>
            <?php echo $topBlock3; ?>

            <?php if (isset($lastNews))
                echo $lastNews;?>

            <!-- change design button -->
            <?php if(count($themes) > 1):?>
                <form action="" method="post">
                    <input type="submit" value="Переключить на другой дизайн" id="change_design_button">
                    <?php
                    $else_theme = $themes[0];
                    //по нажатию на кнопку выбираем следующую за текущей тему
                    foreach($themes as $tpl)
                        if($tpl != $theme):
                            $else_theme = $tpl;
                            break;
                        endif;
                    ?>
                    <input type="hidden" name="theme" value="<?php echo $else_theme ?>">
                </form>
            <?php endif;?>

        </div>
        <!-- /.rightblock -->
    </div>
    <!-- /#right -->
</div>
<!-- /#container -->
<div id="footer">
    <?php echo $banner4; ?>
    <div style="font-size: x-small;">
        Разработка: <a href="http://phpdreamer.ru/" target="_blank">phpdreamer.ru</a>.
    </div>
</div>
<!-- кнопка "Вверх" -->
<a href="#" id="toTop" class="opacity90 shadow">Вверх</a>
<script type="text/javascript">
    $.fn.scrollToTop = function () {
        $(this).hide().removeAttr("href");
        if ($(window).scrollTop() != "0") {
            $(this).fadeIn("slow")
        }
        var scrollDiv = $(this);
        $(window).scroll(function () {
            if ($(window).scrollTop() == "0") {
                $(scrollDiv).fadeOut("slow")
            } else {
                $(scrollDiv).fadeIn("slow")
            }
        });
        $(this).click(function () {
            $("html, body").animate({scrollTop: 0}, "slow")
        })
    };
    $("#toTop").scrollToTop();
</script>
<!-- / кнопка "Вверх" -->
</body>
</html>