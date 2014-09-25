<style type="text/css">
    .users a {
        color: #000;
    }
</style>

<form action="" method="post">

    <b>Дополнительные поля при регистрации:</b><br>
    <?php if (isset ($fields) && is_object($fields)): ?>
        <?php foreach ($fields as $f): ?>
        <p class="q<?php echo $f->id; ?>">
            <input type="text" value="<?php echo $f->name; ?>" class="form-control"
                   style="display: inline-block; width: auto;">
            тип
            <select class="btn btn-sm btn-default">
                <?php foreach ($types as $t): ?>
                    <?php if ($t == $f->type): ?>
                        <option value="<?php echo $t->id; ?>"><?php echo $t->name;?></option>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php foreach ($types as $t): ?>
                    <?php if ($t != $f->type): ?>
                        <option value="<?php echo $t->id; ?>"><?php echo $t->name;?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            можеть быть пустым <input type="checkbox" <?php if ($f->empty) echo 'checked="1"'; ?>
                                      class="btn btn-sm btn-default">
            &nbsp;
            <img class="saveIt" alt="Сохранить" src="<?php echo url::base(); ?>images/save.png" title="Сохранить">
            <img class="removeIt" alt="Удалить" src="<?php echo url::base(); ?>images/delete.png" title="Удалить">
        </p>

    <?php endforeach; ?>
        <br>
        <script type="text/javascript">
            $('.saveIt').css('cursor', 'pointer');
            $('.removeIt').css('cursor', 'pointer');
            $('.saveIt').click(function () {
                var myP = $(this).parent().children();
                var idv = $(this).parent().attr('class');
                $(myP[0]).attr('disabled', 1);
                $.post('<?php echo url::base();?>ajax/fields', { id: idv.split('q')[1], name: $(myP[0]).val(), type: $(myP[1]).val(), empty: $(myP[2]).attr('checked') }, function (data) {
                    $('input').removeAttr('disabled');
                });
            });
            $('.removeIt').click(function () {
                var idv = $(this).parent().attr('class');
                $(this).parent().hide();
                $.post('<?php echo url::base();?>ajax/fields', { id: idv.split('q')[1] }, function (data) {
                });
            });
        </script>
    <?php endif; ?>
    <p class="badge">
        Название <input type="text" name="name" class="form-control" style="display: inline-block; width: auto;">
        тип
        <select name="type" class="btn btn-sm btn-default">
            <?php foreach ($types as $t): ?>
                <option value="<?php echo $t->id; ?>"><?php echo $t->name;?></option>
            <?php endforeach; ?>
        </select>
        можеть быть пустым <input name="empty" type="checkbox" class="btn btn-sm btn-default">
        <input type="submit" value="добавить" class="btn btn-sm btn-default">
    </p>
</form>
<hr><br><b>Поиск пользоватей:</b><br>
<p>
    id <input id="searchById" size="5" type="text" class="form-control" style="display: inline-block; width: auto;">
    <input type="button" value="Найти" class="btn btn-sm btn-default"
           onclick="document.location.href = '<?php echo url::base() ?>admin/user/' + $('#searchById').val()">
</p>
<br>
<div class="users">
    Последние 10 зарегистрированных:<br>
    <ul>
        <?php if (is_object($users)) foreach ($users as $user): ?>
            <li>
                <a href="<?php echo url::base() ?>admin/user/<?php echo $user->id; ?>">
                    <?php echo $user->username;?>
                </a>
                <?php echo $user->profile;?>
            </li>
        <?php endforeach;?>
    </ul>
    <?php if (is_array($bestUsers) && count($bestUsers)): ?>
        <br>
        Лучшие 10 покупателей:
        <ul>
            <?php foreach ($bestUsers as $user): ?>
                <li>
                    <a href="<?php echo url::base() ?>admin/user/<?php echo $user->id; ?>"><?php echo trim($user->username);?></a>
                    <?php echo $user->profile;?>
                </li>
            <?php endforeach;?>
        </ul>
    <?php endif; ?>
</div>
<hr><br>
<form action="" method="post">
    <b>Отправить всем пользователям email</b><br>
    Заголовок <input type="text" size="70" maxlength="200" name="title" value="" class="form-control"
                     style="display: inline-block; width: auto;">

    <p>
        Текст:<br>
        <textarea cols="80" id="editor1" name="editor" rows="10"></textarea>
        <script type="text/javascript" src="<?php echo url::base(); ?>js/ckedit/inc.js"></script>
        <br>Отправлять только пользователям которые не заходили последние
        <input type="text" name="time" value="0" class="form-control" style="display: inline-block; width: auto;"> дней.
    </p>
    <p align="center">
        <input type="submit" value="Разослать" class="btn btn-sm btn-default">
    </p>
</form>