<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<style type="text/css">a{color: #000;}</style>
<h2>Редактирование описания:<br> <a id="prod_link" href="<?php echo $link; ?>"><?php echo $name; ?></a></h2>

<form action="" method="post">
    <p>
        <label for="price">Цена:</label> <input id="price" name="price" size="7" type="text" value="<?php echo $price; ?>">
        <label for="whs">В наличии:</label> <input id="whs" name="whs" size="4" type="text" value="<?php echo $whs; ?>">
    </p>
    <p>
        <textarea cols="80" id="editor1" name="editor" rows="10"><?php echo $html;?></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p class="text-center">
        <input class="btn btn-lg" type="submit" value="Сохранить" />
    </p>
</form>

<?php echo View::factory('admin/editMeta'); ?>
<div class="text-center">
    <button class="btn btn-lg" onclick="edit_meta_by_path($('#prod_link').attr('href'));">Редактировать title и meta</button>
</div>
