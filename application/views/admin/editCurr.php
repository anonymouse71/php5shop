<?php defined('SYSPATH') or die('No direct script access.'); ?>
<form action="" method="post" style="padding: 30px">
    <input type="submit" name="update_from_cbr" value="Обновить все валюты по данным ЦБРФ"/>
</form>

<ul style="list-style: circle" title="Валюты">
    <?php if (is_array($array)) foreach ($array as $code => $num): ?>

        <li>
            <form method="post" action="">
                <p>
                    <input type="text" maxlength="3" size="3" value="<?php echo $code; ?>" disabled="1">
                    <input type="text" value="<?php echo $num; ?>">
                    <img class="dosave" alt="Сохранить" src="<?php echo url::base(); ?>images/save.png"
                         title="Сохранить">
                    <img class="removeit" alt="Удалить" src="<?php echo url::base(); ?>images/delete.png"
                         title="Удалить">
                </p>
            </form>
        </li>
    <?php endforeach; ?>
    <li>
        <form method="post" action="<?php echo url::base() ?>ajax/manageCurr/1">
            <p>
                <input type="text" name="code" maxlength="3" size="3" value="">
                <input type="text" name="val" value="">
                <input type="submit" value="добавить">
            </p>
        </form>
    </li>
</ul>
<div id="ansver">
    <?php echo $errors; ?>
</div>

<script type="text/javascript">
    $('.dosave').css('cursor', 'pointer');
    $('.removeit').css('cursor', 'pointer');
    $('#ansver').ajaxError(function () {
        $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span>");
        $(this).show("slow");
        $('#submit').removeAttr('disabled');
    });
    $('.dosave').click(function () {
        obj = $(this).parent().children();
        $.post('<?php echo url::base();?>ajax/manageCurr/2', { code: $(obj[0]).val(), val: $(obj[1]).val() }, function (data) {
            $('#ansver').html(data);
            $('#ansver').show("slow");
        });
    });
    $('.removeit').click(function () {
        $(this).attr('disabled', 1);
        obj = $(this).parent().children();
        $.post('<?php echo url::base();?>ajax/manageCurr/3', { code: $(obj[0]).val() }, function (data) {
            if (data == "ok") {
                $('#ansver').html(null);
                $('.removeit[disabled]').parent().parent().parent().hide();
            }
            else {
                $('#ansver').html(data);
                $('#ansver').show("slow");
                $('.removeit').removeAttr('disabled');
            }
        });
    });
    $('#ansver').click(function () {
        $(this).hide(1000);
    });

    $('#info').css('width', 'auto');
</script>