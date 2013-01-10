<?php defined('SYSPATH') or die('No direct script access.');
/*
 * php5shop
 * Список переменных для подстановки:
 *
 * title                                - заголовок страницы
 * logo                                 - блок для logo
 * keywords                             - ключевые слова для поисковых систем
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
 */
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $title;?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="ru">
<base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base();?>">
<meta name="copyright" content="<?php echo $_SERVER['HTTP_HOST'];?>">
<meta name="keywords" content="<?php echo $keywords;?>">
<meta name="description" content="<?php echo $title;?>">
<meta name="cmsmagazine" content="f36b4b17fe8e41ffb1bc9b164f77b732" >
<link rel="shortcut icon" type="image/ico" href="images/favicon.gif">
<link rel="alternate" type="application/rss+xml" title="RSS" href="rss.xml">
<?php echo $css;?>
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="js/jquery.simplemodal.js"></script>
<!-- 
php5shop - CMS интернет-магазина
Copyright (C) 2010-2012 phpdreamer, php5shop.com
-->

</head>

<body>
    <div id="header">
        <a href="" class="float"><?php echo $logo;?></a>																																																		<div style="position:absolute;top:1px;left:1px;height:0px;width:0px;overflow:hidden"><h1><a href="http://phpdreamer.ru/" target="_blank">http://phpdreamer.ru/</a></h1><h1><a href="http://php5shop.com">free php shop CMS</a></h1></div>


        <?php echo $loginForm;?>

        <div class="blocks" style="float: right; ">
            <!-- Блок поиска -->
            <img src="images/top_bg.gif" alt="" width="218" height="12">
            <p style="margin-left:20px;"><input type="text" id="inputsearch" onkeydown="if(isEnterKey(this, event)) my_search();"></p>
            <p>&nbsp;<a href="javascript:void(0);" id="buttonsearch">Поиск</a></p>
            <img src="images/bot_bg.gif" alt="" width="218" height="10"><br>
            <div style="display:none" id="searchresults"></div>
            <script type="text/javascript">
                //<!--
                function isEnterKey(obj, event)  {
                    event = event || window.event;
                    var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : null;
                    if (keyCode == 13) {
                        return true;
                    }
                    return false;
                }
                $('#buttonsearch').click(function(){/*Поиск по продуктам (AJAX)*/
                    my_search();
                });
                function my_search(){
                    $.post('ajax/search/' + encodeURIComponent($('#inputsearch').val()),'[]',function(data){
                        var str = '<'+'h2'+'>'+'Результаты поиска:<'+'/h2'+'><'+'br'+'>';
                        if(data.length == 0)
                            str += 'Ничего не найдено<'+'br'+'>';
                        else
                            for (var k in data)
                                str += '<'+'a h'+'ref="shop/product' + data[k].id + '">'+data[k].name+ '<'+'/a'+'><'+'br'+'>';
                        $('#searchresults').html(str);
                        $('#searchresults').modal();
                        $('#simplemodal-container').css('width', '400px');
                        $('#simplemodal-container').css('height', '350px');
                    },'json');
                }
                //-->
            </script>
            <!-- /Блок поиска -->
        </div>

        <div id="topTitle"><?php echo $topTitle;?></div>


        <!-- Верхнее меню-->
        <ul id="menu">
            <li><img src="images/li.gif" alt="меню" width="19" height="29"></li>
            <?php if($menu[1]):?><li><a href=""><img src="images/main.jpg" alt="Главная страница магазина"></a></li><?php endif;?>
            <?php if($menu[2]):?><li><a href="shop/blog"><img src="images/blog.jpg" width="121" height="29" alt="Новости магазина"></a></li><?php endif;?>
            <?php if($menu[3]):?><li><a href="shop/contacts"><img src="images/contact.jpg" alt="Контакты и адреса" ></a></li><?php endif;?>
            <?php if($menu[4]):?><li><a href="shop/clients"><img src="images/clients.jpg" alt="Наши клиенты" ></a></li><?php endif;?>
            <?php if($menu[5]):?><li><a href="admin"><img src="images/admin.jpg" alt="Панель управления администратора" width="121" height="29"/></a></li><?php endif;?>
            <?php if($menu[6]):?><li><a href="shop/user"><img src="images/user.jpg" alt="Личный аккаунт в системе скидок"></a></li><?php endif;?>
            <?php if($menu[7]):?><li><a href="shop/cart"><img src="images/cart.jpg" alt="Покупки"></a></li><?php endif;?>
            <?php if($menu[8]):?>   <li><a href="rss.xml"><img src="images/rss.gif" alt="Лента новостей" ></a></li><?php endif;?>
        </ul>
        <!-- /меню-->
    </div>
    <script type="text/javascript">
	var add = "images/active/";
	var path = document.location.pathname;
	var path2 = "";
	$("ul#menu").find("a").each(function(){
		path2 = $(this).attr('href');
		if(path == "/" + path2 || path2=="" && path=="/"){
			var img = $(this).children('img:first');
			$(img).attr("src",add + $(img).attr("src").split("/")[1]);
	}});
    </script>
    <div id="container">

        <div id="center" class="column">
            <?php echo $banner1;?>
            <div id="content">
                <?php echo $about;?>
                <div class="stuff">
                    <?php echo @$stuff;?>
                </div>
            </div>
        </div>
        <div id="left" class="column">
            <div class="block">
                <div id="navigation">
                    <img src="images/title1.gif" alt="Категории" width="168" >
                    <div style="padding: 15px; padding-top: 0px;">
                        <?php echo $cats;?>
                    </div>
                </div>
            </div>
            <?php echo $banner2;?>
        </div>
        <div id="right" class="column">
            <?php echo $banner3;?>

            <div id="comparebox" class="blocks"<?php if(!strlen($prod1)): /*скрывать блок сравнения если не выбраны товары для сравнения*/ ?> style="display:none"<?php endif;?>>
                <img src="images/top_bg.gif" alt="верх фон" width="218" height="12">
                <img id="deleteCompareBlock" src="images/delete.png" alt="x" title="Закрыть блок" style="float:right;cursor:pointer;" onclick="$('#comparebox').hide(500); $.post('ajax/compare/0');">
                <div align="left" id="comparediv">
                <a href="<?php echo url::base();?>shop/compare"><b><u><span>Сравнить <span id="prcomare1"><?php echo $prod1;?></span> и <span id="prcomare2"><?php echo $prod2;?></span></span></u></b></a>
                </div>
                <img src="images/bot_bg.gif" alt="низ фон" width="218" height="10"><br>
            </div>

            <div class="rightblock">

                <?php echo $topBlock2;?>

                <?php echo @$topBlock1;?>

                <?php echo $topBlock3;?>


                <?php if(isset($lastNews['title'])):?>
                <div class="blocks">
                    <!-- Послендяя запись в блоге-->
                    <img src="images/top_bg.gif" alt="" width="218" height="12">
                    <div style="padding: 10px;">
                        <h3><?php echo $lastNews['title'];?></h3>
                            <?php echo $lastNews['code'];?>
                        <div class="right" align="right" >
                            <a href="shop/blog/<?php echo $lastNews['id'];?>">читать...</a>
                        </div>
                    </div>
                    <img src="images/bot_bg.gif" alt="" width="218" height="10"><br>
                    <!-- /Послендяя запись в блоге-->
                </div>
                <?php endif;?>


            </div><!-- /.rightblock -->
        </div><!-- /#right -->
    </div><!-- /#container -->
    <div id="footer">
        <?php echo $banner4;?>
        <div><small>Разработка: <a href="http://phpdreamer.ru/" target="_blank">phpdreamer.ru</a>.</small></div>
    </div>
    <!-- кнопка "Вверх" -->
    <a href="#" id="toTop" class="opacity90 shadow">Вверх</a>
    <script type="text/javascript">
        $.fn.scrollToTop=function(){$(this).hide().removeAttr("href");if($(window).scrollTop()!="0"){$(this).fadeIn("slow")}var scrollDiv=$(this);$(window).scroll(function(){if($(window).scrollTop()=="0"){$(scrollDiv).fadeOut("slow")}else{$(scrollDiv).fadeIn("slow")}});$(this).click(function(){$("html, body").animate({scrollTop:0},"slow")})};
        $("#toTop").scrollToTop();
    </script>
    <!-- / кнопка "Вверх" -->
</body>
</html>
