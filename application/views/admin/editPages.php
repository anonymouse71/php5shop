<?php defined('SYSPATH') OR die('No direct access allowed.');?>

<form action="" method="post">
<h2>Редактирование дополнительных страниц магазина</h2>

<table>
    <thead>
    <tr>
        <th>Адрес страницы</th>
        <th>Название</th>
        <th>Мета теги</th>
        <th>Содержимое</th>
        <th>Включена</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if (count($pages)):

        echo View::factory('admin/editMeta');

        foreach ($pages as $item):
        ?>
        <tr>
            <td>
                <table style="margin: 0px; border: none; background: inherit">
                    <tr style="border: none">
                        <td style="border: none; background: none;"><?php echo $_SERVER['HTTP_HOST'], url::base() ?></td>
                        <td style="border: none; background: none;">
                            <input type="text" id="path_<?php echo $item->id ?>" class="path_input"
                                   name="path_<?php echo $item->id ?>" required="required"
                                   value="<?php echo htmlspecialchars($item->path) ?>">
                            <div class="alert-danger alert" style="display: none">
                                Использование такого адреса не рекомендуется,
                                возможно он уже используется.
                            </div>
                        </td>
                    </tr>
                </table>
                <a target="_blank" class="go2page"
                     href="<?php echo url::base(), htmlspecialchars($item->path) ?>">Перейти</a>
            </td>
            <td>
                <input type="text" name="name_<?php echo $item->id ?>" value="<?php echo htmlspecialchars($item->name) ?>">
            </td>
            <td>
                <input type="hidden" id="saved_path_<?php echo $item->id ?>" value="<?php echo htmlspecialchars($item->path) ?>">
                <button onclick="edit_meta_by_path('/'+$('#saved_path_<?php echo $item->id ?>').val()); return false;">Редактировать meta</button>
            </td>

            <td>
                <a href="<?php echo url::base() ?>admin/pages#cancel"
                   class="edit_page_button btn-default btn"
                   id="page_b_<?php echo $item->id ?>">Редактировать текст</a>
                <div style="display: none;"><textarea rows="1" cols="1"><?php echo htmlspecialchars($item->text) ?></textarea></div>

            </td>
            <td>
                <input class="page_enabled" type="checkbox" name="enabled_<?php echo $item->id ?>"
                       id="page_enabled_<?php echo $item->id ?>" <?php if($item->enabled) echo'checked'  ?>>
            </td>
            <td>
                <button class="del_page_button btn-danger" id="del_page_<?php echo $item->id ?>">Удалить страницу</button>
            </td>
        </tr>

    <?php
        endforeach;
    ?>
    <tr>
        <td colspan="2" class="text-center"><input type="submit" value="Сохранить все" name="save">
        </td>
        <td colspan="4">
            <span class="alert-info alert" style="font-size: smaller">
        После сохранения или удаления будет обновлена карта сайта и горизонтальное меню.</span>
        </td>
    </tr>

    <?php
    else: ?>
        <tr>
            <td colspan="6">
                <h3>Не создано дополнительных страниц.</h3>
            </td>
        </tr>
    <?php endif ?>
    </tbody>
</table>


</form>
<hr>
<form action="" method="post" id="editForm">
    <h4>Создать новую страницу:</h4>
    <p id="new_page">
        <label for="page_name">Название</label>
        <input required="" type="text" name="name" id="page_name">

    </p>
    <p>
        <textarea cols="80" id="editor1" name="editor" rows="10"></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p>
        <input type="hidden" name="id" id="page_id" value="">
        <input type="submit" value="Создать" name="s" id="page_edit_txt_submit">
        <button type="button" onclick="document.location.href += ''" id="cancel">Отменить</button>
    </p>
</form>

<script>
    $("a.go2page").click(function(){
        if(!$(this).parent().parent().find('input[type=checkbox]:first').prop('checked')){
            alert('Страница не включена. Сначала включите ее.');
            return false;
        }
        return true;
    });
    $(".del_page_button").click(function(){
        var tr = $(this).parent().parent();
        var page_id = $(this).attr('id').split('del_page_')[1];
        $.post(document.location.href, {del: page_id}, function (ok) {
            if(ok != 'ok'){
                document.location.href += '';
            }
            tr.remove();
            if (!$(".del_page_button").length) {
                document.location.href += '';
            }
        }, 'html');
        return false;
    });
    $(".edit_page_button").click(function(){
        $("#page_id").val($(this).attr('id').split('page_b_')[1]);
        CKEDITOR.instances.editor1.setData( $(this).next().text() );
        $("#editForm h4").text('Редактирование страницы '
            + $(this).parent().parent().find('.go2page:first').attr('href'));
        $("#new_page").hide();
        $("#page_edit_txt_submit").val("Сохранить содержимое страницы").click(function(){
            $("#editForm").submit();
        });
        return true;
    });
    $(".path_input").keyup(function(){
        var v = $(this).val().replace('/', ''), alert_div = $(this).next(), show_err = false;
        $(this).val(v);
        var reserved = ['about', 'admin', 'shop', 'blog', 'order', 'login',
            'ajax', 'interkassa', 'save', 'send', 'rss.xml',
            'sitemap.xml', 'robots.txt', 'index.php', '.htaccess',
            'sql.txt', 'template1.css', 'template2.css'];
        $.each(reserved, function (i, vi) {
            if (vi == v) {show_err = true;}
        });
        if (show_err) {
            alert_div.show('slow');
        } else {
            alert_div.hide();
        }
    });
</script>