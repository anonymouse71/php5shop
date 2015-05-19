<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div>Войти:</div>
<script src="//ulogin.ru/js/ulogin.js"></script>

<div id="uLogin"
     data-ulogin="display=panel;fields=first_name;providers=facebook,google,vkontakte,odnoklassniki,yandex,twitter,mailru;hidden=;redirect_uri=http%3A%2F%2F<?php
        echo $_SERVER['HTTP_HOST'], urlencode(url::base()); ?>login">
    <img src="images/loading.gif" alt="loading" />
</div>

