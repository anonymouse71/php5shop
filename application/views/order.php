<?php defined('SYSPATH') or die('No direct script access.');?>

<?php if($message): /*заказ успешно сохранен*/ 
            echo $message;?>
<?php elseif(!$stop && isset($nophone)):  /*регистрация не обязательна, но нет номера телефона*/ ?>

У нас нет Вашего номера телефона. Укажите его, пожалуйста...<br>
<input type="text" id="phone" maxlength="12"> <input type="button" value="Вот мой номер, звоните" id="sendphone">
<script type="text/javascript">
$('#sendphone').click(function(){document.location.href = $('base').attr('href') + 'shop/order/' + $('#phone').val();});
</script>
    
    <?php if(!isset($register)):  /*не зарегистрирован*/ ?>

    <br><br>Рекомендуем Вам сначала зарегистрироваться и указать адрес доставки и другую контактную информацию.

    <?php else:  /*зарегистрирован*/ ?>

    <br><br>Рекомендуем Вам сохранить номер на <a href="shop/user">странице управления аккаунтом</a>.
    <?php endif;?>

<?php elseif($stop && !isset($register)):  /*регистрация обязательна, и ее нет*/ ?>

<br>Вам необходимо <a href="shop/register">зарегистрироваться</a>.

<?php endif;?>

<?php if(!isset($nophone) && !$message):  /*телефон вводить не нужно, заказ еще не сохранен*/ ?>

    <?php if(!isset($way) ):  /*не указан тип оплаты*/ ?>

    <form action="" method="post" id="formway">
    Тип оплаты
    <select id="way" name="way" onchange="$('#formway').submit();">
        <?php foreach($ways as $w):?>
        <option value="<?php echo $w->id;?>"><?php echo $w->name;?></option>
        <?php endforeach;?>
    </select>
    <input type="submit" value="ок">
    </form>

    <?php elseif(isset($way)):  /*указан тип оплаты*/ ?>
        <?php if($way->text):?>
            <?php echo $way->text; ?>
    <br>
    <form action="" method="post" id="formway">
        <p><input type="submit" name="confirm" value="Подтвердить заказ"></p>
    </form>
        <?php endif;?>
    <form action="" method="post" id="formway">
        <p><input type="submit" name="unsetway" value="Выбрать другой способ оплаты"></p>
    </form>
        
    <?php endif;?>

<?php endif;?>