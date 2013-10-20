<?php defined('SYSPATH') or die('No direct script access.'); ?>
<form action="" method="post">
    <div style="margin: 30px;">
        Поиск заказа по номеру
        <div>
            <input type="text" size="3" accesskey="s" name="search" class="form-control" style="width: 60px;float: left;">
            <input type="submit" value="показать" class="btn btn-default right" style="width: 100px; float: left;">
        </div>
    </div>
</form>
<?php if (isset($search_error)):
    echo $search_error;
elseif (isset($array)): ?>

<br>Активные заказы:
<table id="playlist" cellspacing="0" class="table-bordered table-condensed table table-responsive table-striped table-hover table-condensed">
    <tbody>
    <tr>
        <th onclick="document.location.href='<?php echo url::base(), 'admin/index/?set_order_desc=',
        (string)(bool)!$sortOrder;?>';" style="cursor: pointer;">Заказ
        </th>
        <th>Клиент</th>
        <th>Телефон клиента</th>
        <th>Статус заказа</th>
        <th>Дата и время заказа</th>
        <th>Адрес доставки</th>
        <th>Дополнительные поля</th>
        <th>Прошло времени</th>

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
            <select data-id="<?php echo $item['id'];?>" class="btn btn-sm btn-default changestatus">
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

<div class="modal fade" id="infoOrder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Информация о заказе</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть окно</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('select.changestatus').change(function () {
        $.get("<?php echo url::base();?>ajax/changestatus/" + $(this).attr('data-id') + "/" + encodeURIComponent($(this).val()));
    });
    $('.actOrder').click(function () {
        $.get("<?php echo url::base();?>ajax/orderinfo/" + $(this).html(), function (data) {
            $('#infoOrder').find(".modal-body:first").html(data);
            $('#infoOrder').modal({});
        }, 'html');
    });
    $('.actOrderClean').click(function () {
        $('#infoOrder').html('');
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