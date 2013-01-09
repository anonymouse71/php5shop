<?php defined('SYSPATH') or die('No direct script access.');
?><body>
Это письмо с <?php echo $_SERVER['HTTP_HOST'];?><br>
<br>
По Вашему запросу, пароль от аккаунта на сайте был изменен. <br>
Новый пароль:  <br>
<?php echo $pass;?>             <br>
Вместо логина Вы можете использовать email.
<br>
<br>
Администрация <?php echo $_SERVER['HTTP_HOST'];?>
</body>