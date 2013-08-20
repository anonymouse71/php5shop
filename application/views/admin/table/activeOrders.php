<?php defined('SYSPATH') or die('No direct script access.'); ?>
<form action="" method="post">
    <div style="margin: 10px;">
        Поиск заказа по номеру <input type="text" size="3" accesskey="s" name="search">
        <input type="submit" value="показать">
    </div>
</form>
<?php if (isset($search_error)):
    echo $search_error;
elseif (isset($array)): ?>

<br>Активные заказы:
<table id="playlist" cellspacing="0">
    <tbody>
    <tr class="selected">
        <td onclick="document.location.href='<?php echo url::base(), 'admin/index/?set_order_desc=',
        (string)(bool)!$sortOrder;?>';" style="cursor: pointer;">Заказ
        </td>
        <td>Клиент</td>
        <td>Телефон клиента</td>
        <td>Статус заказа</td>
        <td>Дата и время заказа</td>
        <td>Адрес доставки</td>
        <td>Дополнительные поля</td>
        <td>Прошло времени</td>

    </tr>
        <?php foreach ($array as $item): ?>
    <tr>
        <td><a href="javascript:void(0);" class="actOrder"><?php echo $item['id'];?></a></td>
        <td><?php echo (isset($item['user_id']) && isset($item['username'])) ?
            '<a href="' . url::base() . 'admin/user/' . $item['user_id'] . '">' . $item['username'] . '</a>'
            :
            'Не зарег.';
            ?></td>
        <td><?php echo $item['phone'];?></td>
        <td>
            <select id="<?php echo $item['id'];?>">
                <option><?php echo $item['status'];?></option>
                <?php foreach ($item['else_status'] as $st): ?>
                <option><?php echo $st;?></option>
                <?php endforeach;?>
            </select>
        </td>
        <td><?php echo $item['date'];?></td>
        <td><?php echo $item['address'];?></td>
        <td><?php echo $item['contacts'];?></td>
        <td><?php echo $item['difference'];?></td>
    </tr>
        <?php endforeach;?>

    </tbody>
</table>
<?php
else: ?>
<p>Активных заказов сейчас нет</p>
<?php endif; ?>

<script type="text/javascript">

    $('#info').html($('#info').html() + '<' + 'di' + 'v ' + 'i' + 'd="in' + 'foOrder"' + '>' + '</' + 'di' + 'v' + '>');
    $('select').change(function () {
        $.get("<?php echo url::base();?>ajax/changestatus/" + $(this).attr('id') + "/" + encodeURIComponent($(this).val()));
    });
    $('.actOrder').click(function () {
        $.get("<?php echo url::base();?>ajax/orderinfo/" + $(this).html(), function (data) {
            $('#infoOrder').html(data);
            document.location.hash = 'info';
        });
    });
    $('.actOrderClean').click(function () {
        $('#infoOrder').html(null)
    });

    var ordc = parseInt($('#ordcount').html());
    function ordchk() {
        $.get('<?php echo url::base();?>ajax/countOrders', null, function (data) {
            var tmpOrdc = parseInt(data);
            if (tmpOrdc != ordc) {
                $("#wnd1").trigger('click');
                document.location.href += '';
            }
        });
    }
    setInterval(ordchk, 60000);
</script>