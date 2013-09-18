<?php defined('SYSPATH') or die('No direct script access.'); ?>
<h1>Оформление заказа</h1>

<?php if ($message): /*заказ успешно сохранен*/
    echo '<br>', $message;
    if (isset($way, $amount, $order_id) && $way->id == 4) //interkassa
        echo '<form name="payment" action="https://www.interkassa.com/lib/payment.php" method="post"
                    enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
            <input type="hidden" name="ik_shop_id" value="', Model_Apis::get('ik_shop_id'), '">
            <input type="hidden" name="ik_payment_amount" value="', $amount, '">
            <input type="hidden" name="ik_payment_id" value="', $order_id, '">
            <input type="hidden" name="ik_baggage_fields" value="', $order_id, '">
            <input type="hidden" name="ik_payment_desc" value="Заказ номер ', $order_id, '">
            <input type="submit" name="process" value="Оплатить">
            </form>';
    else {}

elseif (!$stop): /* регистрация не обязательна */
    ?>

<?php if (!$register): /*не зарегистрирован*/ ?>

<br>Рекомендуем Вам сначала войти через один из аккаунтов, иконки которых в верху страницы. <br>
Это позволит Вам повторно использовать Вашу контактную информацию при следующем заказе.<br><br>

<?php endif; ?>

<?php echo $userInfo; ?>
<br>

<?php
elseif ($stop && !$register): /*регистрация обязательна, и ее нет*/ ?>

<br>Войдите через один из аккаунтов, иконки которых в верху страницы. <br>
Это необходимо чтобы Вы могли повторно использовать Вашу контактную информацию.<br>
Регистрация обязательна и не займет много времени.

<?php
elseif ($stop && $register): /*регистрация обязательна, и она есть*/
    echo $userInfo;
endif;

if (!$message): /*заказ еще не сохранен*/
    ?>

<script type="text/javascript">
    function submitForm(i) {
        if (i == 1) {
            $('#userForm').append('<input type="hidden" name="way" value="' + $('#way').val() + '">');
        } else {
            $.each($('#formway' + i).find('p,div'), function (i, e) {
                $(e).hide();
            });

            $('#userForm').append($('#formway' + i).html());
        }
        $('#userForm').submit();
    }
</script>

<?php if (!isset($way)): /*не указан тип оплаты*/ ?>

<div class="left70">
    <form action="" method="post" id="formway1">
        <div>Тип оплаты
            <select id="way" name="way" onchange="submitForm(1);">
                <?php foreach ($ways as $w): ?>
                <option value="<?php echo $w->id;?>"><?php echo $w->name;?></option>
                <?php endforeach;?>
            </select>
            <input type="button" value="Ok" onclick="submitForm(1);">
        </div>
    </form>
</div>


<?php elseif (isset($way)): /*указан тип оплаты*/ ?>

<form action="" method="post" id="formway3" style="margin: 15px;">
    <input type="hidden" name="unsetway" value="true" id="unsetway">

    <p><input type="button" value="Выбрать другой способ оплаты" onclick="submitForm(3);"></p>
</form>

<?php if ($way->id): ?>
    <?php echo $way->text; ?>
    <br>
    <form action="" method="post" id="formway2" style="margin-top: 20px;">
        <input type="hidden" name="confirm" value="true" id="confirm">

        <p style="float: right; font-size: large;">
            <?php if ($errors)
            echo 'Исправьте контактные данные и нажмите:';
        else
            echo 'Чтобы подтвердить свое согласие, нажмите:';?>

            <input type="button" name="confirm" value="Подтвердить заказ" onclick="submitForm(2);"
                   style="font-size: large;">
        </p>
    </form>
    <?php endif; ?>


<?php endif;
endif;?>