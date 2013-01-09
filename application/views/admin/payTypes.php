<?php defined('SYSPATH') or die('No direct script access.');?>
<h2>Способы оплаты</h2>
<?php foreach($types as $type): ?>
<div id="pt<?php echo $type->id;?>">
    <a href="<?php echo $path;?>paytypes/<?php echo $type->id;?>"><?php echo htmlspecialchars($type->name);?></a>
    <input type="checkbox" class="activate" <?php if($type->active):?>checked="1"<?php endif;?>>
    <span <?php if($type->active):?>style="display:none;"<?php endif;?>>(выкл.)</span>
</div>
<?php endforeach;?>
<script type="text/javascript">
/* включение и выключение типов оплаты */
$(".activate").click(function(){
var parentdiv = $(this).parent();
var iddiv = $(parentdiv).attr('id').split('t')[1];
var chlds = $(parentdiv).children();
$(chlds[2]).toggle();
if($(this).attr('checked') == true)
    $.post("<?php echo $path;?>paytypes/",{ id: iddiv, checked: 1 });
else
    $.post("<?php echo $path;?>paytypes/",{ id: iddiv, checked: 0 });
});
</script>
<?php if(isset($Type)):?>
<div>
    <br>
    <a href="<?php echo $path;?>paytypes">Добавить способ оплаты</a>    
</div>
<br>
<h3>Редактирование способа: <?php echo htmlspecialchars('"' . $Type->name . '"');?></h3>
<form action="" method="post">
    <p>
        Название <input size="70" type="text" name="newname" value="<?php echo htmlspecialchars($Type->name);?>">
    </p>
    <p>
        Информация \ реквизиты <br>
        <textarea cols="80" id="editor1" name="editor" rows="10"><?php echo htmlspecialchars($Type->text);?></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p>
        <input type="submit" value="Сохранить">
    </p>
</form>
<form action="" method="post">
    <input type="submit" name="del" value="Удалить">
</form>
<?php else:?>
<h3>Добавить способ:</h3>
<form action="" method="post">
    <p>
        Название <input type="text" name="newname" size="70">
    </p>
    <p>
        Информация \ реквизиты <br>
        <textarea cols="80" id="editor1" name="editor" rows="10"></textarea>
        <script type="text/javascript" src="<?php echo url::base();?>js/ckedit/inc.js"></script>
    </p>
    <p>
        <input type="submit" value="Добавить">        
    </p>
</form>
<?php endif;?>



