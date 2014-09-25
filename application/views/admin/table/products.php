<?php defined('SYSPATH') or die('No direct script access.'); ?>
<h3>Импорт товаров в магазин</h3>

<?php if (isset($import)): ?>
    <p class="alert alert-danger" style="cursor: pointer;" onclick="$(this).hide();">
        <?php echo $import;?></p>
<?php endif; ?>

<div id="import" style="width: 600px;">
    <?php echo $info;?>
    <p>
        Если файл для импорта загружен на сервер <br>и находится по адресу
        <input type="text" id="uploaded" size="50"
               value="<?php echo $_SERVER['DOCUMENT_ROOT'] ?>/example.xls"><br>
        нажмите <input type="button" id="addit" value="добавить">
        <img src="images/loading.gif" alt="loading" id="loadingimg" style="display: none;">
    </p>

    <form action="ajax/products/1" method="post" ENCTYPE="multipart/form-data">
        <p>
            Если файл еще не загружен, Вы можете загрузить его сейчас (во временную директорию).
            Товары сразу импортируются и файл будет удален.
            <input type="file" name="importfile">
            <input type="submit" value="добавить">
        </p>
    </form>
    <hr>
</div>

<h3>Каталог товаров</h3>
<?php if ($select): ?>
    Показывать только товары категории <?php echo $select; ?> <input type="button" id="changeGlobalCat" value="да!">
    <br>
    <?php if (count($cats)): ?>
        <div class="text-right">
            <a href="admin/products#chPrice" style="color:black; text-decoration: none; ">повлиять на все цены ▼</a>
            &nbsp;&nbsp;&nbsp;
            <button onclick="save_all_products();"><img alt="Сохранить" src="images/save.png"> Сохранить все</button>
        </div>
    <?php endif;
endif;?>
<br>
<script type="text/javascript">

    $('#changeGlobalCat').click(function () {
        val = $('select').val();
        chldr = $('select').children();
        for (var k in chldr)
            if ($(chldr[k]).val() == val) {
                document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'].url::base();?>admin/products/' + $(chldr[k]).attr('id');
                break;
            }
    });
    $('#addit').click(function () {
        $(this).attr('disabled', 1);
        $('#uploaded').attr('disabled', 1);
        $('#loadingimg').show();
        $.post('ajax/products/1', { filename: $('#uploaded').val() }, function (data) {
            $('#ansver').html(data);
            $('#ansver').show();
            $('#loadingimg').hide();
            $('#uploaded').removeAttr('disabled');
            $('#addit').removeAttr('disabled');
        });
    });

</script>
<p id="ansver" class="alert alert-success" style="display: none; cursor: pointer;"></p>
<script type="text/javascript">

    $('#ansver').ajaxError(function () {
        $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span>");
        $(this).show("slow");
        $('#submit').removeAttr('disabled');
    });
    $('#ansver').click(function () {
        $(this).hide("slow");
    });

</script>
<?php if (count($cats)): ?>



    <table id="playlist" cellspacing="0">
        <tbody>
        <tr class="selected">
            <td>№</td>
            <td>название</td>
            <td>адрес для ЧПУ</td>
            <td>категория</td>
            <td>цена <?php echo $currency;?></td>
            <td>наличие</td>
            <td>фото</td>
            <td></td>
        </tr>
        <?php foreach ($cats as $item): ?>
            <?php $imageURL = url::base() . 'images/products/' . $item->id . '.jpg';
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . $imageURL;
            ?>
            <tr>
                <td><?php echo $item->id;?></td>
                <td><input size="35" type="text" value="<?php echo htmlspecialchars($item->name); ?>"></td>
                <td><input size="35" type="text" value="<?php echo htmlspecialchars($item->path); ?>"></td>
                <td><?php echo $item->cat;?></td>
                <td><input size="7" type="text" value="<?php echo $item->price; ?>"></td>
                <td><input size="4" maxlengt="4" type="text" value="<?php echo (int)$item->whs; ?>"></td>
                <td>
                    <?php
                    if (file_exists($imagePath))
                        echo'<a href="' . $imageURL . '">есть</a>';
                    else
                        echo'нет';
                    ?>
                    <a href="admin/images/<?php echo $item->id; ?>" target="_blank"
                       onClick="popupWin = window.open(this.href, 'contacts', 'location,width=700,height=600,top=50,scrollbars=1,location=0,menubar=0,resizable=1,status=0,toolbar=0'); popupWin.focus(); return false;">
                        <img class="editd" alt="edit" src="images/edit.png" title="Редактировать изображения">
                    </a>
                </td>
                <td>
                    <img class="saveIt" alt="Сохранить" src="images/save.png" title="Сохранить">
                    <a href="admin/description/<?php echo $item->id; ?>"><img class="editd" alt="edit"
                                                                              src="images/edit.png"
                                                                              title="Редактировать описание"></a>
                    <img class="removeIt" alt="Удалить" src="images/delete.png" title="Удалить">
                </td>
            </tr>
        <?php endforeach;?>

        </tbody>
    </table>
    <p class="text-right">
        <button onclick="save_all_products();"><img alt="Сохранить" src="images/save.png" > Сохранить все</button>
    </p>
    <script type="text/javascript">

        $('.saveIt').css('cursor', 'pointer');
        $('.removeIt').css('cursor', 'pointer');
        $('.editd').css('border', '0');
        $('.saveIt').click(function () {
            myP = $(this).parent().parent().children();
            pcat = 0;
            val = $(myP[3]).children().val();
            chldr = $('select').children();
            for (var k in chldr)
                if ($(chldr[k]).val() == val) {
                    pcat = $(chldr[k]).attr('id');
                    break;
                }
            $.post('ajax/products/2', {
                    id: $(myP[0]).html(),
                    name: $(myP[1]).children().val(),
                    path: $(myP[2]).children().val(),
                    price: $(myP[4]).children().val(),
                    whs: $(myP[5]).children().val(),
                    cat: pcat
                },
                function (data) {
                    $('#ansver').html(data);
                    $('#ansver').show();
                });
        });
        $('.removeIt').click(function () {
            $(this).parent().parent().hide();
            $.post('ajax/products/3', { id: $(this).parent().parent().children().html() });
        });

        function save_all_products(){
            $("#saving_info1").html('Происходит сохранение товаров <img src="images/loading.gif" alt="Загрузка...">');
            $("#saving_info2").html("");
            $('#modalSaving').modal({});

            var count_all = $('.saveIt').length;
            var count_saved = 0;
            var error_prods = [];
            var post_resp = 0;

            $("#saving_info2").html("0 из " + count_all);

            function saved_plus_one(){
                count_saved += 1;

                if(count_saved == count_all){
                    $("#saving_info1").html("Товары успешно сохранены.");
                    $("#saving_info2").html("Это окно можно закрыть.");
                }else {
                    $("#saving_info2").html(count_saved + ' из ' + count_all);
                }
            }

            $('.saveIt').each(function () {
                myP = $(this).parent().parent().children();
                pcat = 0;
                val = $(myP[3]).children().val();
                chldr = $('select').children();
                for (var k in chldr)
                    if ($(chldr[k]).val() == val) {
                        pcat = $(chldr[k]).attr('id');
                        break;
                    }
                var data2save = {
                    id: $(myP[0]).html(),
                    name: $(myP[1]).children().val(),
                    path: $(myP[2]).children().val(),
                    price: $(myP[4]).children().val(),
                    whs: $(myP[5]).children().val(),
                    cat: pcat
                };
                $.post('ajax/products/2', data2save, function (data) {
                    post_resp += 1;
                    if(data == 'Успешно сохранено!'){
                        saved_plus_one();
                    }else {
                        error_prods.push(data2save);
                    }
                });
            });

            var intervalID = setInterval(function () {
                if(post_resp == count_all){
                    clearInterval(intervalID);
                    if(error_prods.length){
                        $.each(error_prods, function(i, data2save){
                            $.post('ajax/products/2', data2save, function (data) {
                                if(data == 'Успешно сохранено!'){
                                    saved_plus_one();
                                }
                            });
                        });
                    }
                }
            }, 500);
        }


    </script>

    <div class="modal fade" id="modalSaving" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Сохранение</h4>
                </div>
                <div class="modal-body">
                    <div id="saving_info1"></div>
                    <div id="saving_info2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть окно</button>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>В категории товаров нет</p>
<?php endif; ?>
<?php if (count($cats)): ?>
    <br>
    <div id="chPrice">
        <b>Изменить все цены </b>на <input type="text" size="3"> процентов, исключая категории с номерами <input
            type="text" size="6"> (через запятую)
        <input type="button" value="ок" id="button2changePrices">
    </div>
    <br>
    <script type="text/javascript">
        $("#button2changePrices").click(function () {
            $.post('ajax/changePrice',
                {a: $(this).prev().val(), p: $(this).prev().prev().val()},
                function () {
                    document.location.href = 'admin/products';
                });
        });
    </script>
<?php endif; ?>

<form id="addItemForm" action="/admin/products#addItemForm" method="POST">
    Добавить товар: <input type="text" name="add_item" value="" size="35">
    <input type="submit" value="Сохранить">
</form>