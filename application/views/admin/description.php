<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<style type="text/css">a{color: #000;}</style>
<h2>Редактирование описания:<br> <a href="<?php echo $link; ?>"><?php echo $productname; ?></a></h2>

<form action="" method="post">    
    <p>
        <textarea cols="80" id="editor1" name="editor" rows="10"><?php echo $html;?></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p>
        <input type="submit" value="Сохранить" />
    </p>
</form>