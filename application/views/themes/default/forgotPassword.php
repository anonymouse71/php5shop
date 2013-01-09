<?php defined('SYSPATH') or die('No direct script access.');?>
<?php if($errors !== TRUE):?>
<div class="left70">
<form id="registerForm" action="login/emailpass" method="post">
    <table border="0" width="350px">
        <tr>
            <td><span>Email:</span> </td>
            <td><input class="line" type="text" name="email"></td>
        </tr>
        <tr>
            <td><span>Символы с картинки:</span> <?php echo $captcha;?></td>
            <td><input class="line" type="text" name="captcha"></td>
        </tr>
        <tr align="center">
            <td>&nbsp;</td>
            <td ><input type="submit" name="submit" value="Отправить запрос"></td>
        </tr>
    </table>
    <div id="errors" style="color:red;">
        <?php echo $errors;?>
    </div>
</form>
</div>
<?php else:?>
<p class="line">
    <span>На Ваш email отправлено письмо. Перейдите по ссылке из письма чтобы сменить пароль.</span>
    Если письмо не пришло, свяжитесь с администрацией.
</p>
<?php endif;?>