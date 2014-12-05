<?php defined('SYSPATH') or die('No direct script access.'); ?>
    <h2>Способы оплаты</h2>
<?php foreach ($types as $type): ?>
    <div id="pt<?php echo $type->id; ?>">
        <a href="<?php echo $path; ?>paytypes/<?php echo $type->id; ?>"><?php echo htmlspecialchars($type->name);?></a>
        <input type="checkbox" class="activate" <?php if ($type->active): ?>checked="1"<?php endif;?>>
        <span <?php if ($type->active): ?>style="display:none;"<?php endif;?>>(выкл.)</span>
    </div>
<?php endforeach; ?>
    <script type="text/javascript">
        /* включение и выключение типов оплаты */
        $(".activate").click(function () {
            var parentdiv = $(this).parent();
            var chlds = $(parentdiv).children(),
                post_data = {
                    id: $(parentdiv).attr('id').split('t')[1],
                    checked: $(this).prop('checked')?'1':'0'
                };
            $(chlds[2]).toggle();
            $.post("<?php echo $path;?>paytypes/", post_data);
        });
    </script>
<?php if (isset($type4edit)): ?>
    <div>
        <br>
        <a href="<?php echo $path; ?>paytypes">Добавить способ оплаты</a>
    </div>
    <br>
    <h3>Редактирование способа: <?php echo htmlspecialchars('"' . $type4edit->name . '"');?></h3>
    <form action="" method="post">
        <p>
            Название <input size="70" type="text" name="newname" value="<?php echo htmlspecialchars($type4edit->name); ?>">
        </p>
        <p>
            Информация \ реквизиты <br>
            <textarea cols="80" id="editor1" name="editor"
                      rows="10"><?php echo htmlspecialchars($type4edit->text);?></textarea>
            <script type="text/javascript" src="<?php echo url::base(); ?>js/ckedit/inc.js"></script>
        </p>
        <p>
            <input type="submit" value="Сохранить">
        </p>
    </form>
    <?php if ($type4edit->id != 4): //don't delete intrkassa  ?>
        <form action="" method="post">
            <input type="submit" name="del" value="Удалить">
        </form>
    <?php
    else:
        echo $interkassa;
    endif;?>
<?php else: ?>
    <h3>Добавить способ:</h3>
    <form action="" method="post">
        <p>
            Название <input type="text" name="newname" size="70">
        </p>

        <p>
            Информация \ реквизиты <br>
            <textarea cols="80" id="editor1" name="editor" rows="10"></textarea>
            <script type="text/javascript" src="<?php echo url::base(); ?>js/ckedit/inc.js"></script>
        </p>
        <p>
            <input type="submit" value="Добавить">
        </p>
    </form>
<?php endif;