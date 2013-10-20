<?php defined('SYSPATH') or die('No direct script access.');?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $title;?></title>
    <base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base(); ?>">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

    <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-migrate-1.2.1.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/bootstrapizator.js"></script>
    <?php echo $head;?>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
    <ul class="nav navbar-nav">
        <li><a href="<?php echo $path; ?>"><b>Активные заказы</b></a></li>
        <li><a href="<?php echo $path; ?>user"><b>Клиенты</b></a></li>
        <li><a href="<?php echo $path; ?>groups"><b>Группы</b></a></li>
        <li><a href="<?php echo $path; ?>config"><b>Настройки</b></a></li>
        <li><a href="<?php echo $path; ?>curr"><b>Валюты</b></a></li>
        <li><a href="<?php echo $path; ?>categories"><b>Категории</b></a></li>
        <li><a href="<?php echo $path; ?>products"><b>Товары</b></a></li>
        <li><a href="<?php echo $path; ?>blog"><b>Блог</b></a></li>
        <li><a href=""><b>Перейти к магазину</b></a></li>
    </ul>
</nav>
<div style="margin: 20px;">
    <?php echo $body;?>
</div>
<div class="menu">&nbsp;</div>
<p align="right" style="width: 1024px;">
    <small>Copyright &copy; <a href="http://phpdreamer.ru/" target="_blank">phpdreamer.ru</a></small>
</p>
</body>
</html>