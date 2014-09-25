<?php defined('SYSPATH') or die('No direct script access.');?>
<h2>SEO</h2>
<p>Этот раздел дает возможность привязать значения meta-тегов и тега title к страницам магазина.</p>
<?php if($error):?><p class="alert alert-danger"><?php echo htmlspecialchars($error)?></p><?php endif; ?>
<form action="" method="post">
    <fieldset>
        <legend>Добавить Meta</legend>
        <label for="new_path">Для страницы:</label><br>
        <input id="new_path" name="path" placeholder="/page_uri" maxlength="255" size="100"
            <?php if (isset($_GET['search_path']) && !count($meta))
                echo 'value="', htmlspecialchars($_GET['search_path']), '"';?>
            >
        <br>
        <small>*Указывайте адрес страницы начиная с символа /, который следуюет за доменом сайта.
            Пример: /page/contacts
        </small>
        <br>
        <label for="new_title">Заголовок страницы (title)</label><br>
        <input id="new_title" name="title" maxlength="300" size="100"><br>

        <label for="new_description">meta description</label><br>
        <textarea id="new_description" name="description" maxlength="300" rows="3" cols="100"></textarea><br>

        <label for="new_keywords">meta keywords</label><br>
        <textarea id="new_keywords" name="keywords" maxlength="300" rows="3" cols="100"></textarea><br>
        <br><input type="submit" value="Сохранить" name="new_save">
    </fieldset>
</form>
<br><br>
<fieldset>
    <legend>Редактировать Meta</legend>
</fieldset>
<form action="" method="GET">

    <label for="search_path">Поиск страницы по URI:</label><br>
    <input id="search_path" name="search_path" placeholder="/page_uri" maxlength="255" size="100"
        <?php if (isset($_GET['search_path']))
            echo 'value="', htmlspecialchars($_GET['search_path']), '"';?>
        >
    <br><br>
    <input type="submit" value="Найти">
    <?php if (isset($_GET['search_path']))
        echo '<a href="/admin/meta" class="btn btn-default">Отменить поиск, показать все</a>';?>
</form>
<br>
<hr>

<?php if (count($meta)):
    echo View::factory('admin/editMeta')
    ?>
    <table>
        <thead>
        <tr>
            <th>URI</th>
            <th>title</th>
            <th>description</th>
            <th>keywords</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($meta as $item): ?>
            <tr id="meta_<?php echo $item->id ?>">
                <?php foreach(array('path', 'title', 'description', 'keywords') as $k)
                    echo '<td>',htmlspecialchars($item->$k),'</td>' ?>
                <td><a href="javascript:void(0);" class="btn btn-default edit_meta"><img alt="edit" src="images/edit.png" title="Редактировать"></a>
                    <a href="javascript:void(0);" class="btn btn-default del_meta"><img alt="Удалить" src="images/delete.png" title="Удалить"></a>
                </td>
            </tr>

        <?php endforeach ?>
        </tbody>
    </table>
    <?php echo $pagination ?>
<?php else: ?>
    <p class="alert alert-info">Еще не добавлено значений для meta-тегов
        <?php if (isset($_GET['search_path']))
            echo 'искомой страницы';
        else
            echo 'страниц.';?>
    </p>
<?php endif; ?>



<script type="text/javascript">
    $('.del_meta').click(function () {
        if (confirm("Точно удалить?")){
            var id = $(this).parent().parent().attr('id').split('_')[1];
            var tr = $(this).parent().parent();

            $.post(document.location.href, {del: id}, function(answer){
                if(answer != 'ok'){
                    alert('Произошла ошибка. Страница будет обновлена. ');
                    document.location.href += '';
                    return false;
                }
                tr.remove();
            }, 'text');
        }
    });

    $('.edit_meta').click(function () {
        var tr = $(this).parent().parent();
        var data = {id: $(this).parent().parent().attr('id').split('_')[1]};
        var fields = ['path', 'title', 'description', 'keywords'];
        $.each(fields, function (i, v) {
            data[v] = $(tr.find('td')[i]).text();
        });
        edit_meta_modal(data, function (post_data) {
            $.each(fields, function (i, v) {
                $(tr.find('td')[i]).text(post_data[v]);
            });
        });
    });
</script>