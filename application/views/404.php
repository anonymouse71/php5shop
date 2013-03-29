<?php defined('SYSPATH') or die('No direct script access.'); ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>404</title>
    <base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base(); ?>">
</head>
<body>
<div style="text-align: center; margin-top: 100px">
    <a href="<?php echo url::base() ?>"><img src="images/404.jpg" alt="404"><br></a>

    <h2><?php echo __('Страница не найдена')?>.</h2>
    <?php echo __('Но можно вернуться на')?> <a href="<?php echo url::base() ?>"><?php echo __('главную')?></a>
</div>
</body>
</html>