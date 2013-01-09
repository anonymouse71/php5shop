<?php defined('SYSPATH') or die('No direct script access.');?>
<!--
<input type="button" value="Импорт товаров в магазин" onclick="$('#import').toggle();"> 
<input type="button" value="Экспорт товаров из магазина" onclick="document.location.href='ajax/export';">
<br><br>
-->
<h3>Импорт товаров в магазин</h3>

<?php if(isset($import)): ?> <p style="background-color: #F0F0F0; padding: 10px; cursor: pointer;" onclick="$(this).hide();"><?php echo $import;?></p> <?php endif;?>
    
<div id="import" style="width: 600px;" >
    <?php echo $info;?>
    <p>
        Если файл для импорта загружен на сервер <br>и находится по адресу
        <input type="text" id="uploaded" size="50"
               value="<?php echo $_SERVER['DOCUMENT_ROOT']?>/example.xls"><br>
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
Показывать только товары категории <?php echo $select;?> <input type="button" id="changeGlobalCat" value="да!">
<br>
<?php if(count($cats)): ?>
<div align="right">
    <a href="admin/products#chPrice" style="color:black; text-decoration: none; ">повлиять на все цены ▼</a>
</div>
<?php endif;
endif;?>
<br>
<script type="text/javascript">
<!--
$('#changeGlobalCat').click(function(){
    val = $('select').val();
    chldr = $('select').children();
    for(var k in chldr)
       if($(chldr[k]).val() == val){
            document.location.href = 'http://<?php echo $_SERVER['HTTP_HOST'].url::base();?>admin/products/' + $(chldr[k]).attr('id');
            break;
        }
});
$('#addit').click(function(){
$(this).attr('disabled',1);
$('#uploaded').attr('disabled',1);
$('#loadingimg').show();
$.post('ajax/products/1',{ filename: $('#uploaded').val() },function (data){
       $('#ansver').html(data);
       $('#ansver').show();
       $('#loadingimg').hide();
       $('#uploaded').attr('disabled', 0);
       $('#addit').attr('disabled', 0);
});
});
-->
</script>
<p id="ansver" style="background-color: #F0F0F0; padding: 10px; display: none; cursor: pointer;"></p>
<script type="text/javascript">
<!--
$('#ansver').ajaxError(function() {
  $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span>");
  $(this).show("slow");
  $('#submit').attr('disabled', 0);
});
$('#ansver').click(function(){$(this).hide("slow");});
-->
</script>
<?php if(count($cats)): ?>

<table id="playlist" cellspacing="0">
    <tbody>
        <tr class="selected">
            <td>№</td>
            <td>название</td>
            <td>категория</td>            
            <td>цена <?php echo $currency;?></td>
            <td>наличие</td>
            <td>фото</td>            
            <td></td>
        </tr>
        <?php foreach($cats as $item):?>
        <?php $imageURL = url::base() . 'images/products/' . $item->id . '.jpg';
          $imagePath = $_SERVER['DOCUMENT_ROOT'] . $imageURL;
        ?>
        <tr>
            <td><?php echo $item->id;?></td>
            <td><input size="35" type="text" value="<?php echo htmlspecialchars($item->name);?>"></td>
            <td><?php echo $item->cat;?></td>           
            <td><input size="7" type="text" value="<?php echo $item->price;?>"></td>
            <td><input size="4" maxlengt="4" type="text" value="<?php echo (int) $item->whs;?>"></td>
            <td>
                <?php
                if(file_exists($imagePath))
                    echo'<a href="' . $imageURL . '">есть</a>';
                else
                    echo'нет';
                ?>
                <a href="admin/images/<?php echo $item->id;?>" target="_blank"
                   onClick="popupWin = window.open(this.href, 'contacts', 'location,width=700,height=600,top=50,scrollbars=1,location=0,menubar=0,resizable=1,status=0,toolbar=0'); popupWin.focus(); return false;">
                    <img class="editd" alt="edit" src="images/edit.png" title="Редактировать изображения">
                </a>
            </td>
            <td>
                <img class="saveIt" alt="Сохранить" src="images/save.png" title="Сохранить">
                <a href="admin/description/<?php echo $item->id;?>"><img class="editd" alt="edit" src="images/edit.png" title="Редактировать описание"></a>
                <img class="removeIt" alt="Удалить" src="images/delete.png" title="Удалить">
            </td>
        </tr>
        <?php endforeach;?>

    </tbody>
</table>
<script type="text/javascript">
<!--
$('.saveIt').css('cursor', 'pointer');
$('.removeIt').css('cursor', 'pointer');
$('.editd').css('border', '0');
$('.saveIt').click(function(){    
    myP = $(this).parent().parent().children(); 
    pcat = 0;
    val = $(myP[2]).children().val();
    chldr = $('select').children();
    for(var k in chldr)
       if($(chldr[k]).val() == val){
            pcat = $(chldr[k]).attr('id');
            break;
        }
    $.post('ajax/products/2',{ id: $(myP[0]).html(), name: $(myP[1]).children().val(), price: $(myP[3]).children().val(), whs: $(myP[4]).children().val(), cat: pcat },function (data){
       $('#ansver').html(data);
       $('#ansver').show();
    });
});
$('.removeIt').click(function(){    
    $(this).parent().parent().hide();
    $.post('ajax/products/3',{ id: $(this).parent().parent().children().html() });    
});

-->
</script>
<?php else:?>
<p>В категории товаров нет</p>
<?php endif;?>
<?php if(count($cats)): ?>
<br>
<div id="chPrice">
    <b>Изменить все цены </b>на <input type="text" size="3"> процентов, исключая категории с номерами <input type="text" size="6"> (через запятую)
    <input type="button" value="ок" onclick="$.post('ajax/changePrice',{a: $(this).prev().val(), p: $(this).prev().prev().val()});document.location.href='admin/products';">
</div>
<br>
<?php endif;?>