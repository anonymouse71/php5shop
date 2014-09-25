<?php defined('SYSPATH') or die('No direct script access.');?>
<style type="text/css">
a{
text-decoration: none;
color: #000;
}
.level1{padding-left: 15px;}
.level2{padding-left: 30px;}
.level3{padding-left: 45px;}
.level4{padding-left: 60px;}
</style>

<table border="0">
    <tr>
        <td><b>Выбор <span style="color:green;">категории 1</span>:</b>
            <div id="cat1">
                <div>
                    <div style="padding-left: 0px; padding-top: 3px;">
                        <a href="javascript:void(0);#0" class="category-item" title="Корневая категория">Корневая категория</a>
                    </div>
                </div>
                <div style="padding-left: 15px;">
                      <?php echo $cats;?>
                </div>
            </div>
        </td>
        <td width="60"></td>
        <td>
            <b>Выбор <span style="color:blue;">категории 2</span>:</b>
            <div id="cat2">
                <div>
                    <div style="padding-left: 0px; padding-top: 3px;">
                        <a href="javascript:void(0);#0" class="category-item" title="Корневая категория">Корневая категория</a>
                    </div>
                </div>

                <div style="padding-left: 15px;">
                      <?php echo $cats;?>
                </div>
            </div>
        </td>
    </tr>
</table>
<h5>
Категорий в магазине: <?php echo $countCats;?></h5>

<p>&nbsp;</p>
<div id="whattodo" style="display: none;">
    <?php if($countCats > 0):?>
    <p>
        Чтобы установить
        <input type="text" id="inputnew" value=" новое название" onclick="if($(this).val()==' новое название')$(this).val('');">
        для <span style="color:green;">категории 1</span>
        и перенести ее в <span style="color:blue;">категорию 2</span>,
        нажмите на <input type="button" id="updatebutton" value="эту кнопку">
        <br>
        * Если не хотите переносить, выберете <span style="color:blue;">категорию 2</span>, которая на уровень выше <span style="color:green;">категории 1</span>
    </p>
    <?php endif;?>
    <p>
        Чтобы создать подкатегорию для <span style="color:green;">категории 1</span>,
        введите <input type="text" id="catnewname" value=" название сюда" onclick="if($(this).val()==' название сюда')$(this).val('');" >
        и нажминте <input type="button" id="createnew" value="эту кнопку">
    </p>
    <?php if($countCats > 0):?>
    <p>
        <br>
        Есть еще кнопка <input id="delcats" type="button" value="Удалить категорию 1 с подкатегориями и товарами в них">
    </p>
    <?php endif;?>

     
    <form action="" method="post">
        <input type="submit" value="Редактировать описание категории">
        <input type="hidden" id="ed" name="ed" value="">
    </form>
    
</div>
<p>&nbsp;</p>
<div id="ansver"><?php echo $errors;?></div>
<?php if(isset($_POST['ed']) && $_POST['ed']): ?>
<div id="editD">
        <form action="" method="post">
            <p>
                <textarea cols="80" id="editor1" name="editor" rows="10"><?php echo $html; ?></textarea>
                <script type="text/javascript" src="js/ckedit/inc.js"></script>
            </p>
            <p>
                <input type="submit" value="Сохранить">
                <input type="hidden" id="descrid" name="descrid" value="<?php echo (string)(int)($_POST['ed']);?>">

            </p>
        </form>
    </div>

 <?php endif;   ?>
<script type="text/javascript" language="JavaScript">
function reloadcats(){
document.location.href = document.location.href;
}
$('#ansver').ajaxError(function() {
  $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span> и обновите страницу.");
  $(this).show("slow");
  $('#submit').removeAttr('disabled');
});

$('.category-item').click(function (){
    $('#editD').hide();
    id = $(this).parent().parent().parent().attr('id');
    if(id == 'cat1'){
        $('#cat1').find('.category-item').css('color','black');        
        $(this).css('color','green');
        $('#ansver').data('selected1',this.href);
        $('#infocatid').css('color','green');

    }
    else {
        $('#cat2').find('.category-item').css('color','black');
        $(this).css('color','blue');
        $('#ansver').data('selected2',this.href);
        $('#infocatid').css('color','blue');
    }
    catid = this.href.split('#')[1];
    $('#infocatid').html(catid);    
    $('#ed').attr('value',catid);
    $('#whattodo').show(500);
    $('#infoid').show();
    
});
$('#updatebutton').click(function (){
if(!$('#ansver').data('selected1') || !$('#ansver').data('selected2') )
   $('#ansver').html('Вы должны выбрать 2 категории: редактируемую и категорию, в которую ее перенести');
else            
   $.post('ajax/categ/2',{ id: $('#ansver').data('selected1').split('#')[1], parentId: $('#ansver').data('selected2').split('#')[1], val: $('#inputnew').val() },function (data){
       reloadcats();
    });
    
});
$('#createnew').click(function (){
if(!$('#ansver').data('selected1'))
    $('#ansver').html('Вы должны выбрать категорию 1, которая будет родительской для новой категории');
else
    $.post('ajax/categ/1',{ id: $('#ansver').data('selected1').split('#')[1], val: $('#catnewname').val() },function (data){
        reloadcats();
    });
});
$('#delcats').click(function (){
if(!$('#ansver').data('selected1'))
    $('#ansver').html('Вы должны выбрать категорию');
else
    $.post('ajax/categ/3',{ id: $('#ansver').data('selected1').split('#')[1] },function (data){
        reloadcats();
    });
});
$('#ansver').click(function (){$(this).hide(1000);});
</script>

