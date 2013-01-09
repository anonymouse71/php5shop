<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<script type="text/javascript" src="<?php echo url::base();?>js/ckedit/ckeditor.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("<div/>", {"class": "errorCkeditor"}).appendTo("form");
    $(".errorCkeditor").html("<h1>Ваш браузер не поддерживает визуальный редактор ckeditor!</h1> Для работы на этой странице необходимо использовать совместимый браузер. Попробуйте <a href=\"http://www.mozilla-europe.org/ru/\">Firefox</a>.");
    $(".errorCkeditor").hide();
    for (var i=1000; i<10000;i+=1000) //10 секунд на загрузку редактора, проверка загрузки каждую секунду
    setTimeout(function(){
        if($("#cke_editor1").attr('dir') != 'ltr')    //редактор не загружен
            $(".errorCkeditor").show();            //браузер не поддерживается
        else
            $(".errorCkeditor").hide();             //все работает
    }, i);

});
</script>