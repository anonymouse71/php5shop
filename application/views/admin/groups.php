<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php foreach ($groups as $item): ?>
    <div>
        <p>
            Группа <input id="g<?php echo $item->id; ?>" type="text" size="50" value="<?php echo $item->name; ?>">
            скидка <input type="text" size="5" value="<?php echo 100 * (1 - $item->pct); ?>">%
            <img class="saveIt" alt="Сохранить" src="images/save.png" title="Сохранить">
            <img class="removeIt" alt="Удалить" src="images/delete.png" title="Удалить">
        </p>
    </div>
<?php endforeach; ?>
<form method="post" action="<?php echo url::base() ?>ajax/groups/1">
    <p>
        Добавить <input type="text" size="50" name="name"> со скидкой <input type="text" size="5" name="pct"
                                                                             value="0.00">
        <input type="submit" value="ok">
    </p>
</form>
<div>
    <p id="ansver">
        <?php echo $errors;?>
    </p>
</div>
<script type="text/javascript">
    $('.saveIt').css('cursor', 'pointer');
    $('.removeIt').css('cursor', 'pointer');
    $('.saveIt').click(function () {
        myP = $(this).parent().children();
        $.post('ajax/groups/2', { id: myP[0].id.split('g')[1], name: $(myP[0]).val(), pct: $(myP[1]).val() }, function (data) {
            $('#ansver').html(data);
        });
    });
    $('.removeIt').click(function () {
        myP = $(this).parent().children();
        $(this).parent().hide();
        $.post('ajax/groups/3', { id: myP[0].id.split('g')[1] }, function (data) {
            $('#ansver').html(data);
        });
    });
</script>