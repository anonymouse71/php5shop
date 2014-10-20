<?php defined('SYSPATH') or die('No direct script access.');
/*
 * php5shop
 * Список переменных для подстановки:
 *
 * title                                - заголовок страницы
 * logo                                 - блок для logo
 * keywords                             - meta keywords для поисковых систем
 * description                          - meta description для поисковых систем
 * head                                 - дополнительные вставки кода в head
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
    <meta name="viewport" content="initial-scale = 1.0,user-scalable=yes" />
    <base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base(); ?>">
    <meta name="copyright" content="<?php echo $_SERVER['HTTP_HOST']; ?>">
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="cmsmagazine" content="f36b4b17fe8e41ffb1bc9b164f77b732">
    <link rel="shortcut icon" type="image/ico" href="images/favicon.gif">
    <link rel="alternate" type="application/rss+xml" title="RSS" href="rss.xml">

    <link rel="stylesheet" href="themes_public/mobile1/css/gumby.css" type="text/css">
    <link rel="stylesheet" href="themes_public/mobile1/css/mobile1.css" type="text/css">

    <script src="js/jquery-1.9.1.min.js" type="text/javascript"></script>
    <script src="js/jquery-migrate-1.2.1.js" type="text/javascript"></script>

    <script src="themes_public/mobile1/js/modernizr-2.6.2.min.js"></script>

    <script src="themes_public/mobile1/js/gumby.js"></script>
    <script src="themes_public/mobile1/js/ui/gumby.toggleswitch.js"></script>
    <script src="themes_public/mobile1/js/gumby.init.js"></script>



    <?php echo $head; ?>
    <!--
    <?php if (isset($lastNews)): ?>
        <script type="text/javascript" src="js/jquery.slides.min.js"></script>
    <?php endif; ?>
    -->

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
                <div class="row">
                    <div id="logo" class="twelve columns"><a href=""><?php echo $logo; ?></a></div>
                </div>

                <div class="row">
                    <div class="twelve columns top_title">
                        <?php echo $topTitle; ?>
                    </div>
                </div>

                <section class="tabs vertical">
                    <ul class="tab-nav" id="menu">
                        <?php if ($menu[1]): ?>
                            <li><a href="">Главная</a>
                            </li>
                        <?php endif;
                        if ($menu[2]): ?>
                            <li><a href="blog">Новости</a></li>
                        <?php endif;
                        foreach ($special_pages as $page_uri => $page_label): ?>
                            <li><a href="<?php echo htmlspecialchars($page_uri); ?>"><?php
                                    echo htmlspecialchars($page_label); ?></a></li>
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

                </section>

            </div>
            <!-- END header -->
        </div>
        <div id="content">

            <div class="main">
                <div class="row">
                    <div class="six columns">
                        <div class="module_LoginForm">
                            <?php echo $loginForm; ?>
                        </div>
                    </div>

                    <div class="six columns">


                        <div id="search">

                            <div class="search">

                                <div class="append field">

                                    <input id="inputsearch" class="wide input" type="email" placeholder="Найти товар">
                                    <div class="medium primary btn">
                                        <a href="javascript:void(0);" class="switch" id="buttonsearch" gumby-trigger="#search_modal" >Поиск</a>
                                    </div>
                                    <a id="open_modal" href="javascript:void(0);" class="switch" gumby-trigger="#search_modal" ></a>
                                </div>



                            </div>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="two columns">
                        <?php echo @$topBlock1; ?>
                    </div>

                    <div class="two columns">
                        <?php echo $topBlock2; ?>
                    </div>

                    <div class="three columns">
                        <?php if(count($themes) > 1):?>

                            <form action="" method="post" class="field centered" style="float: right">
                                <div class="medium primary btn">
                                    <input type="submit" value="Переключить на другой дизайн" id="change_design_button" class="switch">
                                </div>
                                <?php
                                $else_theme = $themes[0];
                                //по нажатию на кнопку выбираем следующую за текущей тему
                                $found = false;
                                foreach ($themes as $tpl)
                                    if ($found)
                                    {
                                        $else_theme = $tpl;
                                        break;
                                    } elseif ($tpl == $theme)
                                        $found = true;
                                ?>
                                <input type="hidden" name="theme" value="<?php echo $else_theme ?>">
                            </form>
                        <?php endif;?>
                    </div>
                </div>


                <div class="wrapper">
                    <div id="right">
                        <div class="wrapper">
                            <section class="tabs vertical">
                            <ul class="tab-nav">
                                <li>
                                    <a data-content="cats_tab" href="javascript:void(0);" class="nav_toggle">Категории</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="cats_tab">
                                <?php echo $cats; ?>
                            </div>
                            </section>

                            <div class="extra-indent">


                                <?php echo $banner3; ?>

                                <?php echo $topBlock3; ?>

                                <?php //if (isset($lastNews))
                                    //echo $lastNews;?>

                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div id="breadcrumbs"><?php echo $breadcrumbs; ?></div>

                        <?php echo $banner1; ?>
                        <?php echo $about; ?>
                        <div class="stuff">
                            <?php if (isset($stuff)) echo $stuff; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php echo $banner2; ?>
        <div class="main">
            <div id="footer">
                    <?php echo $banner4; ?>
                    <div style="text-align: right;font-size: x-small;">
                        Разработка: <a href="http://phpdreamer.ru/" target="_blank">phpdreamer.ru</a>.
                    </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="search_modal">
    <div class="content">
        <a class="close switch" gumby-trigger="|#search_modal"><i class="icon-cancel"/></i></a>

        <div class="row">
            <div class="ten columns centered text-center">
                <div id="searchresults"></div>
                <p class="btn primary medium">
                    <a href="#" class="switch" id="search_close"
                       gumby-trigger="|#search_modal">Закрыть</a>
                </p>
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

    $(".nav_toggle").click(function(){
        var tab = $('#' + $(this).attr('data-content'));
        if(!tab.hasClass('active'))
            tab.addClass('active');
        else
            tab.removeClass('active');
    });

    /* simple_modal replacement */
    $.fn.modal = function (options) {
        $("#searchresults").html($(this).html());
        return $("#open_modal").trigger('click');
    };

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
        $('#searchresults').html('Загрузка...');
        $.post('ajax/search/' + encodeURIComponent($('#inputsearch').val()), '[]', function (data) {
            var str = '<' + 'h2' + '>' + 'Результаты поиска:<' + '/h2' + '><' + 'br' + '>';
            if (data.length == 0)
                str += 'Ничего не найдено<' + 'br' + '>';
            else{
                str += '<ul class="field">';
                for (var k in data)
                    str += '<li class="li_search"><' + 'a h' + 'ref="shop/product' + data[k].id + '">' + data[k].name + '<' + '/a' + '><' + '/li' + '>';
                str += '</ul>';
            }

            $('#searchresults').html(str);
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