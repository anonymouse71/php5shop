<?php defined('SYSPATH') or die('No direct script access.'); ?>
<form action="" method="POST">
    <h2>Настройки интеркассы:</h2>

    Для приема платежей через шлюз Интеркасса, Вам необходимо зарегистрировать магазин на
    <a href="http://interkassa.com/index.php?ref_id=23597">www.interkassa.com</a><br />
    и указать здесь настройки со страницы &quot;Настройка магазина&quot;:

    <p>Идентификатор магазина (ik_shop_id):
        <input type="text" name="ik_shop_id" value="<?php echo htmlspecialchars($ik_shop_id);?>" style="width: 400px"/>
    </p>

    <p>Ваш секретный ключ (secret_key):
        <span>
            <input type="password" name="ik_secret_key" value="<?php
            echo htmlspecialchars($ik_secret_key);?>" style="width: 200px" id="ik_secret_key"/>
            <input type="button" value="показать" onclick="
                $(this).parent().html('<input type=text name=ik_secret_key value=\''
                + $('#ik_secret_key').val() + '\' style=\'width: 300px\' />');"
                />
        </span>
    </p>
    <div>Данные для указания в личном кабинете интеркассы:</div>
    <small><i>Настройки кассы - Интерфейс</i></small>
    <p>
        URL успешной оплаты: <strong>http://<?php echo $_SERVER['HTTP_HOST'];?>/interkassa/success</strong>
        <br />
        URL неуспешной оплаты: <strong>http://<?php echo $_SERVER['HTTP_HOST'];?>/interkassa/fail</strong>
        <br />
        URL ожидания проведения платежа: <strong>http://<?php echo $_SERVER['HTTP_HOST'];?>/</strong>
        <br />
        URL взаимодействия: <strong>http://<?php echo $_SERVER['HTTP_HOST'];?>/interkassa/status/<?php
            echo Controller_Interkassa::getStatusPageId() ?></strong>
        <br /> Тип запроса: <strong>POST</strong>

    </p>
    <div style="padding: 20px; width: 60%"><i>Внимание! Помните, что &quot;URL взаимодействия&quot; изменится
            при удалении первого администратора или изменении его email!
            В таком случае нужно будет изменить &quot;URL взаимодействия&quot; и в кабинете интеркассы.
        </i></div>


    <p>
        <input type="submit" value="Сохранить настройки"/>
    </p>
</form>

<script type="text/javascript">
    if ($("#ik_secret_key").val() == '') {
        $("#ik_secret_key").parent().find('input[type=button]').hide();
    }
    $("#ik_secret_key").keyup(function () {
        if ($("#ik_secret_key").val() != '') {
            $("#ik_secret_key").parent().find('input[type=button]').show();
        }
    });
</script>