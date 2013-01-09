<?php defined('SYSPATH') or die('No direct script access.');
?><body>
Это письмо с <?php echo $_SERVER['HTTP_HOST'];?><br>
<br>
С IP <?php echo $_SERVER['REMOTE_ADDR'];?> был сделан запрос на изменение пароля. <br>
Если это сделали Вы, перейдите по ссылке http://<?php echo $_SERVER['HTTP_HOST'] . url::base() . 'login/emailpass/' . $id;?><br>
Важно сделать это в том же браузере, с которого был отправлен запрос.<br>
После этого придет письмо с новым паролем.<br>
<br>
<br>
Администрация <?php echo $_SERVER['HTTP_HOST'];?>

</body>
