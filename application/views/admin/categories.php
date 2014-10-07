<?php defined('SYSPATH') or die('No direct script access.');?>
<style type="text/css">
    a {
        text-decoration: none;
        color: #000;
    }
    .level1 {
        padding-left: 15px;
    }
    .level2 {
        padding-left: 30px;
    }
    .level3 {
        padding-left: 45px;
    }
    .level4 {
        padding-left: 60px;
    }
    .categories_list {
        padding-left: 15px;
    }
    #createnew, #updatebutton {
        margin: 7px;
    }
    #ansver{
        color: red;
    }
</style>
<?php echo View::factory('admin/editMeta'); ?>
<div id="ansver" class="alert"><?php echo $errors; ?></div>
<table border="0">
    <tr>
        <td><b>Выбор <span style="color:green;">категории 1</span>:</b>

            <div id="cat1">
                <div style="padding-left: 0; padding-top: 3px;">
                    <a href="javascript:void(0);#0" class="category-item" title="Корневая категория">Корневая
                        категория</a>
                </div>
                <div class="categories_list">
                    <?php echo $cats; ?>
                </div>
            </div>
        </td>
        <td width="60"></td>
        <td>
            <b>Выбор <span style="color:blue;">категории 2</span>:</b>

            <div id="cat2">
                <div style="padding-left: 0; padding-top: 3px;">
                    <a href="javascript:void(0);#0" class="category-item" title="Корневая категория">Корневая
                        категория</a>
                </div>
                <div class="categories_list">
                    <?php echo $cats; ?>
                </div>
            </div>
        </td>
    </tr>
</table>

<h5>Категорий в магазине: <?php echo $countCats; ?></h5>

<p>&nbsp;</p>
<div id="whattodo" style="display: none;">
    <?php if ($countCats > 0): ?>
        <p>
            Чтобы установить новое название
            <input type="text" id="inputnew" placeholder="новое название" maxlength="50">

            и путь <?php echo url::base() ?>category/<input type="text" id="pathnew" maxlength="255">

            для <span style="color:green;">категории 1</span>
            <br>
            или перенести ее в <span style="color:blue;">категорию 2</span>,
            нажмите на <input type="button" id="updatebutton" value="эту кнопку">
            <br>
            * Если не хотите переносить, выберите <span style="color:blue;">категорию 2</span>, которая на уровень выше
            <span style="color:green;">категории 1</span>
            или просто не выбирайте <span style="color:blue;">категорию 2</span>
        </p>
    <?php endif; ?>
    <p>
        Чтобы создать подкатегорию для <span style="color:green;">категории 1</span>,
        введите<br/>
        <label for="catnewname">название</label>
        <input type="text" id="catnewname" maxlength="50">,
        <label for="catnewpath">путь</label>
        <?php echo url::base() ?>category/<input type="text" id="catnewpath" maxlength="255">
        <br/>
        и нажминте <input type="button" id="createnew" value="эту кнопку">
    </p>
    <?php if ($countCats > 0): ?>
        <p>
            <br>
            Есть еще кнопка <input id="delcats" type="button"
                                   value="Удалить категорию 1 с подкатегориями и товарами в них">
        </p>
    <?php endif; ?>


    <form action="" method="post">
        <input type="submit" value="Редактировать описание">
        <span style="color:green;">категории 1</span>
        <input type="hidden" id="ed" name="ed" value="">
    </form>
    <br>
    <button id="b_meta">Редактировать title и meta</button>
    для <span style="color:green;">категории 1</span>

</div>


<?php if (isset($_POST['ed']) && $_POST['ed']): ?>
    <div id="editD">
        <h3>Редактирование описания категории id <?php echo (int)$_POST['ed']; ?></h3>

        <form action="" method="post">
            <p>
                <textarea cols="80" id="editor1" name="editor" rows="10"><?php echo $html; ?></textarea>
                <script type="text/javascript" src="js/ckedit/inc.js"></script>
            </p>
            <p>
                Вы можете использовать разделитель <span class="cke_button_icon cke_button__horizontalrule_icon"
                                                         style="background: url(http://subaru.ccc/js/ckeditor/plugins/icons.png?t=DAED) 0 -336px;background-size:auto;float: none">&nbsp;</span>
                <br/>Содержимое снизу от разделителя будет отображаться под блоком с товарами,
                содержимое сверху от разделителя будет над товарами.
            </p>

            <p>
                <input type="submit" value="Сохранить">
                <input type="hidden" id="descrid" name="descrid" value="<?php echo (int)$_POST['ed']; ?>">
            </p>

        </form>
    </div>

<?php endif; ?>
<script type="text/javascript" language="JavaScript">
    function reloadcats() {
        document.location.href = document.location.href;
    }

    function uriByPath(p) { return '<?php echo url::base()?>category/' + p}

    var pathDict = <?php echo json_encode($catsArray['path']) ?>;

    $(".categories_list a").each(function () {
        var a = $(this);
        var href = a.attr('href').replace(/^https?:\/\/<?php echo preg_quote($_SERVER['HTTP_HOST']) ?>/, ''), href2;
        $.each(pathDict, function (catId, catPath) {
            href2 = uriByPath(catPath.toString());
            if (href == href2) {
                a.attr('href', 'javascript:void(0);#' + catId);
                a.addClass('cat_id' + catId);
            }
        });
    });


    $('#ansver').ajaxError(function () {
        $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span> и обновите страницу.");
    });

    $('.category-item').click(function () {
        $('#editD').hide();
        var id = $(this).parent().parent().parent().attr('id');
        var catid = this.href.split('#')[1];
        var path = pathDict[catid];
        if (id == 'cat1') {
            $('#cat1').find('.category-item').css('color', 'black');
            $(this).css('color', 'green');
            $('#ansver').data('selected1', this.href);
            $('#infocatid').css('color', 'green');

            $('#pathnew').val(path);
            $('#inputnew').val($(this).text());
        }
        else {
            $('#cat2').find('.category-item').css('color', 'black');
            $(this).css('color', 'blue');
            $('#ansver').data('selected2', this.href);
            $('#infocatid').css('color', 'blue');
        }

        $('#infocatid').html(catid);
        $('#ed').attr('value', catid);
        $('#whattodo').show(500);
        $('#infoid').show();
        $("#b_meta").unbind('click').click(function () {
            edit_meta_by_path(uriByPath(path));
        });
    });

    $('#updatebutton').click(function () {
        var post = {
            id: $('#ansver').data('selected1').split('#')[1],
            val: $('#inputnew').val(),
            path: $('#pathnew').val()
        };
        if ($('#ansver').data('selected2')) {
            post['parentId'] = $('#ansver').data('selected2').split('#')[1];
        }
        $.post('ajax/categ/2', post, function () {
            reloadcats();
        });

    });
    $('#createnew').click(function () {
        if (!$('#ansver').data('selected1'))
            $('#ansver').html('Вы должны выбрать категорию 1, которая будет родительской для новой категории');
        else
            $.post('ajax/categ/1', {
                id: $('#ansver').data('selected1').split('#')[1],
                val: $('#catnewname').val(),
                path: $('#catnewpath').val()
            }, function () {
                reloadcats();
            });
    });
    $('#delcats').click(function () {
        if (!$('#ansver').data('selected1'))
            $('#ansver').html('Вы должны выбрать категорию');
        else
            $.post('ajax/categ/3', {id: $('#ansver').data('selected1').split('#')[1]}, function (data) {
                reloadcats();
            });
    });
    $('#ansver').click(function () {
        $(this).html("");
    });
</script>

