<?php defined('SYSPATH') or die('No direct script access.');?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <base href="http://<?php echo $_SERVER['HTTP_HOST'] . url::base();?>">
    </head>
    <body>
        <div style="margin: 40%">
            <form id="loginForm" action="" method="post">
                <p>Пароль:<input type="password" name="pass"><input type="submit" value="Вход"></p>
                <p><a href="shop/forgotpassword" class="reg">Забыли пароль?</a></p>
            </form>
        </div>
    </body>
</html>