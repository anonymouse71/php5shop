<?php defined('SYSPATH') or die('No direct script access.');?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo $title;?></title>
<base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base();?>">
<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
<style type="text/css">
body{font-size: 14px;}
.menu { width: 1024px ; padding:5px 0 0 1em; margin:0; list-style:none; height:40px; position:relative; background:transparent url(images/admin/0c.gif) repeat-x left bottom; font-size:11px;}
.menu li {float:left; height:40px; margin-right:1px;}
.menu li a {display:block; float:left; height:40px; line-height:35px; color:#333; text-decoration:none; font-family:arial, verdana, sans-serif; font-weight:bold; text-align:center; padding:0 0 0 4px; cursor:pointer; background:url(images/admin/0a.gif) no-repeat;}
.menu li a b {float:left; display:block; padding:0 16px 5px 12px; background:url(images/admin/0b.gif) no-repeat right top;}
.menu li.current a {color:#000; background:url(images/admin/2a.gif) no-repeat;}
.menu li.current a b {background:url(images/admin/2b.gif) no-repeat right top;}
.menu li a:hover {color:#000; background: url(images/admin/1a.gif) no-repeat;}
.menu li a:hover b {background:url(images/admin/1b.gif) no-repeat right top;}
.menu li.current a:hover {color:#000; background: url(images/admin/2a.gif) no-repeat; cursor:default;}
.menu li.current a:hover b {background:url(images/admin/2b.gif) no-repeat right top;}
input, textarea, select {border: 1px;border-color: gray;border-style: solid;font-family: Verdana;font-size: 10px;}
</style>
<?php echo $head;?>
</head>
<body onload="stripe('playlist', '#fff', '#edf3fe');">
    <ul class="menu">
        <li><a href="<?php echo $path;?>"><b>Активные заказы</b></a></li>
        <li><a href="<?php echo $path;?>user"><b>Клиенты</b></a></li>
        <li><a href="<?php echo $path;?>groups"><b>Группы</b></a></li>
        <li><a href="<?php echo $path;?>config"><b>Настройки</b></a></li>
        <li><a href="<?php echo $path;?>curr"><b>Валюты</b></a></li>
        <li><a href="<?php echo $path;?>categories"><b>Категории</b></a></li>
        <li><a href="<?php echo $path;?>products"><b>Товары</b></a></li>
        <li><a href="<?php echo $path;?>blog"><b>Блог</b></a></li>
        <li><a href=""><b>Перейти к магазину</b></a></li>
    </ul>
    <div style="margin: 20px;">
        <?php echo $body;?>
    </div>
    <div class="menu">&nbsp;</div>
    <p align="right" style="width: 1024px;"><small>Copyright &copy; <a href="http://phpdreamer.ru/" target="_blank">phpdreamer.ru</a></small></p>
</body>
</html>