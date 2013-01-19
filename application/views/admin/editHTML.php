<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<style type="text/css">a{color: #000;}</style>
<h2>Редактирование дополнительных блоков магазина</h2>
<ul>    
    <li>
        <a href="<?php echo url::base();?>admin/edit/1">Блок о магазине (приветствие)</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/2">Большой баннер в центре страницы</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/3">Баннер под левым блоком</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/4">Баннер над правым блоком</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/5">Footer (низ страницы)</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/6">Страница про клиентов магазина</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/7">Страница контактов</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/8">Логотип</a>
    </li>
    <li>
        <a href="<?php echo url::base();?>admin/edit/9">Заголовок магазина</a>
    </li>
</ul>
<form action="" method="post">
    <p>
        <textarea cols="80" id="editor1" name="editor" rows="10"><?php echo $text;?></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p>
        <input type="submit" value="Сохранить">
    </p>
</form>