<?php defined('SYSPATH') or die('No direct script access.');?>

<b>Приглашение в группу</b>
<select id="selectg">
<?php foreach($groups as $item):?>
<option value="<?php echo $item->id;?>"><?php echo $item->name;?></option>
<?php endforeach;?>
</select>
<input type="button" value="Создать" id="createinv"><br><br>
Созданный код инвайта: <input id="answer" type="text" readonly="1" value="" size="10">
<br><br>
Активных инвайтов: <span id="inactiv"></span> <br>
<p><b>Отменить приглашения:</b></p>
<p>
    <input type="button" value="все" id="clearall"> </p>
<p>
    <input type="button" value="самое новое" id="clearlast1"></p>
<p>
    <input type="button" value="самое старое" id="clearlast2">
</p>
<script type="text/javascript">
function updateCount(){
    $.post('ajax/invite',{count: 1},function (data){
        $('#inactiv').html(data);
    });
}
updateCount();
$('#createinv').click(function(){
    var cat = '' + $('#selectg').val();
    $.post('ajax/invite',{create: cat},function (data){
       $('#answer').val(data);
       updateCount();
    });
});
$('#clearall').click(function(){
    $.post('ajax/invite',{clearall: 1},function (data){
       $('#answer').val(data);
       updateCount();
    });
});
$('#clearlast1').click(function(){
    $.post('ajax/invite',{clearlast: 1},function (data){
       $('#answer').val(data);
       updateCount();
    });
});
$('#clearlast2').click(function(){
    $.post('ajax/invite',{clearlast: 2},function (data){
       $('#answer').val(data);
       updateCount();
    });
});
</script>