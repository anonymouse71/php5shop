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
 * about2                               - блок под блоком товаров
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
 * show_contact_form                    - показывать форму обратной связи
 * price_filter                         - фильтр по цене
 * sortForm                             - выбор сортировки товаров
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

    <link href='http://fonts.googleapis.com/css?family=Oswald&v1' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Quattrocento+Sans' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="template2.css" type="text/css"/>

    <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="js/jquery-migrate-1.2.1.js" type="text/javascript"></script>
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
<body id="body" class="first">
<div class="body-top">
    <div class="splash">
        <div class="main">
            <!-- header -->
            <div id="header">
                <div id="logo"><a href="" class="float"><?php echo $logo; ?></a></div>

                <?php echo @$topBlock1; ?>

                <?php echo $topBlock2; ?>

                <div class="module_LoginForm">
                    <?php echo $loginForm; ?>
                </div>

                <div id="search">
                    <div class="moduletable">

                        <div class="search">
                            <input type="text" class="inputbox" id="inputsearch"
                                   onkeydown="if(isEnterKey(this, event)) my_search();">
                            <a href="javascript:void(0);" id="buttonsearch" class="button">Поиск</a>

                            <div style="display:none" id="searchresults"></div>
                        </div>
                    </div>
                </div>
                <div id="topmenu">
                    <div class="moduletable-nav">
                        <ul class="menu">
                            <li class="item29"><?php echo $topTitle; ?></li>
                        </ul>
                    </div>

                </div>

                <div class="moduletable-categories">

                    <div class='ddsmoothmenu' id='smoothmenu1'>
                        <div id="relative_div" style="position:relative;z-index:0"></div>
                        <ul class="level1" id="menu">
                            <?php if ($menu[1]): ?>
                                <li><a href="">Главная</a>
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
                            if ($menu[6] && !$menu[3]): ?>
                                <li><a href="rss.xml">RSS</a></li>
                            <?php endif; ?>

                        </ul>
                    </div>
                </div>

            </div>
            <!-- END header -->
        </div>
        <div id="content">
            <div class="main">
                <div id="breadcrumbs"><?php echo $breadcrumbs; ?></div>
                <div class="wrapper">
                    <div id="right">
                        <div class="wrapper">
                            <div class="extra-indent">
                                <div class="module-specials">
                                    <h3><span><span>Категории</span></span></h3>

                                    <div class="boxIndent">
                                        <div style="padding: 15px; padding-top: 0px;">
                                            <?php echo $cats; ?>
                                        </div>
                                    </div>
                                </div>

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

                                <?php echo $banner3, $price_filter; ?>

                                <?php echo $topBlock3; ?>

                                <?php if (isset($lastNews))
                                    echo $lastNews;?>

                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <?php echo $banner1; ?>
                        <?php echo $about; ?>
                        <?php echo $sortForm; ?>

                        <div class="stuff" <?php
                        if (isset($oneProductPage))
                            echo 'itemscope itemtype="http://schema.org/Product"'; ?>>
                            <?php echo @$stuff; ?>
                        </div>
                        <?php echo $about2; ?>
                    </div>
                </div>

                <!-- Форма обратной связи -->
                <?php if ($show_contact_form): ?>
                <div id="place_for_contact_form"></div>
                <script>
                    $.get('/ajax/contact_form', null, function(f){$("#place_for_contact_form").html(f)}, 'html');
                </script>
                <?php endif ?>
                <!-- /Форма обратной связи -->

            </div>
        </div>
        <?php echo $banner2; ?>
        <div class="main">
            <div id="footer">
                <div class="space">
                    <div class="wrapper">
                        <div class="footerText">

                            <?php echo $banner4; ?>
                            <div style="text-align: right;font-size: x-small;">
                                Разработка: <a href="http://phpdreamer.ru/" target="_blank">phpdreamer.ru</a>.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</div>
<script type="text/javascript">
    var add = "images/active/";
    var path = document.location.pathname;
    var path2 = "";
    $("ul#menu").find("a").each(function () {
        path2 = $(this).attr('href');
        if (path == "/" + path2) {
            $(this).addClass('active');
        }
    });

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