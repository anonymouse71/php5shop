<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="left70">
<form id="registerForm" action="login/register" method="post">
    <h2>Регистрация</h2>

    <table border="0" width="350px">
        <tr>
            <td><span>Имя:</span> </td>
            <td><input class="line" type="text" name="username" value="<?php if(isset($val['username'])) echo $val['username'];?>"></td>
        </tr>        
        <tr>
            <td><span>Пароль:</span></td>
            <td><input class="line" type="password" name="password" value="<?php if(isset($val['password'])) echo $val['password'];?>"></td>
        </tr>
        <tr>
            <td><span>Пароль еще раз:</span></td>
            <td><input class="line" type="password" name="password_confirm" value="<?php if(isset($val['password_confirm'])) echo $val['password_confirm'];?>"></td>
        </tr>
        <tr>
            <td><span>Email:</span> </td>
            <td><input class="line" type="text" name="email" value="<?php if(isset($val['email'])) echo $val['email'];?>"></td>
        </tr>
        <tr>
            <td><span>Телефон:</span></td>
            <td><input class="line" type="text" name="phone" value="<?php if(isset($val['phone'])) echo $val['phone'];?>"></td>
        </tr>
        <?php if(isset($fields) && is_array($fields)): ?>
        <?php foreach($fields as $field):?>
        <tr>
            <td><span><?php echo $field->name;?>:</span></td>
            <td>
                <?php if($field->type < 5): ?><input class="line" type="text" name="f<?php echo $field->id;?>" value="<?php echo $val['f' . $field->id];?>">
                <?php elseif($field->type == 5):?><input class="line" type="checkbox" name="f<?php echo $field->id;?>" <?php echo (isset($val['f' . $field->id]) && !$val['f' . $field->id])? '' : 'checked="1"';?>>
                <?php elseif($field->type == 6):?><textarea class="line" cols="15" rows="2" name="f<?php echo $field->id;?>"><?php echo ( $val['f' . $field->id] );?></textarea>
                <?php endif;?>
            </td>
        </tr>

        <?php endforeach;?>
        <?php endif;?>
        <tr>
            <td><span>Адрес доставки:</span> </td>
            <td><input class="line" type="text" name="address" value="<?php if(isset($val['address'])) echo $val['address'];?>"></td>
        </tr>
        <tr>
            <td><span>Символы с картинки:</span> <?php echo $captcha;?></td>
            <td><input class="line" type="text" name="captcha"></td>
        </tr>
        <tr align="center">
            <td>&nbsp;</td>
            <td ><input type="submit" name="submit" value="создать аккаунт"></td>
        </tr>
    </table>
    <div id="errors" style="color:red;">
        <?php echo $errors;?>
    </div>
</form>
</div>