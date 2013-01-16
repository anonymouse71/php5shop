<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!--
<img src="images/top_bg.gif" alt="" width="218" height="12">
<form action="login/index" method="post" id="loginForm">
    <p class="line"><span>Имя:</span> <input type="text" name="login" onkeydown="if(isEnterKey(this, event)) $('#passwInputText').focus();"></p>
    <p class="line"><span>Пароль:</span> <input id="passwInputText" type="password" name="pass" onkeydown="if(isEnterKey(this, event)) $('#loginForm').submit();"></p>
    <p class="line center"><a href="shop/register" class="reg">Регистрация</a> | <a href="shop/forgotpassword" class="reg">Забыли пароль?</a></p>
    <p class="line center pad20"><a href="javascript:void(0);"><img src="images/enter.gif" alt="Вход" height="25" width="69" onclick="$('#loginForm').submit();"></a></p>
</form>
<img src="images/bot_bg.gif" alt="" width="218" height="10"><br>
-->

<div style="width: 370px;margin: 2px;float: left;">

    <div style="float: left;margin-right: 12px; margin-top: 9px;">Войти:</div>

    <script src="//ulogin.ru/js/ulogin.js"></script>


    <div id="uLogin"
         data-ulogin="display=panel;fields=first_name;providers=facebook,google,vkontakte,odnoklassniki,yandex,twitter,mailru;hidden=;redirect_uri=http%3A%2F%2F<?php
            echo $_SERVER['HTTP_HOST'], urlencode(url::base()); ?>login">
        <img src="images/loading.gif" alt="loading" />
    </div>


</div>

