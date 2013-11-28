<?php defined('SYSPATH') or die('No direct script access.');?>
<style type="text/css">a{color: #000;}</style>
<h2>Настройки магазина</h2>
<a class="btn btn-info button-lg" href="admin/edit/">Редактирование рекламных блоков и дополнительных страниц магазина</a>
<a class="btn btn-info button-lg"  href="admin/paytypes">Редактирование способов оплаты</a>
<br>
<table border="0" style="margin-top: 10px">
    <tr>
        <td>
            <form action="" method="post" id="userForm">
                <table border="0">
                    <tr>
                        <td><span>В корзину можно добавить больше 1 еденицы товара</span></td>
                        <td><input type="checkbox" name="bigCart" <?php if($bool['bigCart'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Показывать блок выбора валюты</span></td>
                        <td><input type="checkbox" name="currency" <?php if($bool['currency'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Показывать блок с отрывком из последней записи блога</span></td>
                        <td><input type="checkbox" name="LastNews" <?php if($bool['LastNews'])echo 'checked="1"';?>></td>
                    </tr>

                    <tr>
                        <td><span>Оповещать о новых заказах на Jabber </span><input type="text" name="jabber" value="<?php echo $jabber;?>"></td>
                        <td><input type="checkbox" name="ordJabb" <?php if($bool['ordJabb'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Оповещать о новых заказах на Email &nbsp;&nbsp;</span><input type="text" name="email" value="<?php echo $email;?>"></td>
                        <td><input type="checkbox" name="ordMail" <?php if($bool['ordMail'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Блог включен</span></td>
                        <td><input type="checkbox" name="ShowBlog" <?php if($bool['ShowBlog'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Показывать время загрузки страниц магазина</span></td>
                        <td><input type="checkbox" name="timeFooter" <?php if($bool['timeFooter'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Нельзя совершать покупки без регистрации</span></td>
                        <td><input type="checkbox" name="regOrder" <?php if($bool['regOrder'])echo 'checked="1"';?>></td>
                    </tr>

                    <tr>
                        <td><span>Голосование включено</span></td>
                        <td><input type="checkbox" name="poll" <?php if($bool['poll'])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Комментирование включено</span></td>
                        <td><input type="checkbox" name="comments" <?php if($bool['comments'])echo 'checked="1"';?>></td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Название магазина <input type="text" name="shopName" value="<?php echo $shopName;?>" style="width: 60%"></td>
                    </tr>
                    <tr>
                        <td>Ключевые слова для поисковых роботов: <textarea cols="55" rows="1" name="keywords"><?php echo $keywords;?></textarea></td>
                    </tr>
                    <tr>
                        <td>Email от имени которого отправляются письма <input type="text" name="email3" value="<?php echo $email3;?>"><br></td>
                    </tr>
                    <tr>
                        <td><h3>Подключение внешних систем:</h3></td>
                    </tr>
                    <tr>
                        <td>Код для google analytics <small>(<a href="https://www.google.com/analytics/" target="_blank">?</a>)</small> <input size="14" type="text" name="analytics" value="<?php echo $analytics;?>"> (например, UA-12345678-9) </td>
                    </tr>
                    <tr>
                        <td>Код для sape.ru <small>(<a href="http://www.sape.ru/" target="_blank">?</a>)</small> <input type="text" size="32" maxlength="32" name="sape" value="<?php echo $sape;?>"> (32 символа) </td>
                    </tr>
                    <tr>
                        <td>disqus_shortname (<a href="http://disqus.com/" target="_blank">disqus.com</a>) <input type="text" size="32" maxlength="32" name="disqus" value="<?php echo $disqus;?>"> </td>
                    </tr>
                    <tr>
                        <td>vkontakte apiId <small>(<a href="http://vkontakte.ru/developers.php" target="_blank">?</a>)</small> <input type="text" size="32" maxlength="32" name="vkcomments" value="<?php echo $vkcomments;?>"> </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><b>Настройки главного меню магазина.</b> Показывать пункты:</td>
                    </tr>
                    <tr>
                        <td><span>Главная страница</span></td>
                        <td><input type="checkbox" name="menu1" <?php if($menu[1])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Новости (блог)</span></td>
                        <td><input type="checkbox" name="menu2" <?php if($menu[2])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Контакты</span></td>
                        <td><input type="checkbox" name="menu3" <?php if($menu[3])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Доставка и оплата</span></td>
                        <td><input type="checkbox" name="menu4" <?php if($menu[4])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Панель управления (видна только администраторам)</span></td>
                        <td><input type="checkbox" name="menu5" <?php if($menu[5])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Аккаунт</span></td>
                        <td><input type="checkbox" name="menu6" <?php if($menu[6])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Покупки (корзина)</span></td>
                        <td><input type="checkbox" name="menu7" <?php if($menu[7])echo 'checked="1"';?>></td>
                    </tr>
                    <tr>
                        <td><span>Лента новостей (RSS)</span></td>
                        <td><input type="checkbox" name="menu8" <?php if($menu[8])echo 'checked="1"';?>></td>
                    </tr>                    
                    <tr align="center">
                        <td ><input type="button" id="submit" value="сохранить"></td>
                    </tr>
                </table>
                <div>
                    <br>
                    <h4 id="ansver" style="display: none; cursor: pointer;" class="alert alert-info"></h4>
                </div>
            </form>
        </td>
        <td width="50">
        </td>
        <td>
            <b>Статусы заказов:</b>
            <?php foreach($status as $item):?>
            <div>
                <p>
                    <input id="g<?php echo $item->id;?>" type="text" size="23" value="<?php echo $item->name;?>">
                    <img class="saveIt" alt="Сохранить" src="<?php echo url::base();?>images/save.png" title="Сохранить">
                    <img class="removeIt" alt="Удалить" src="<?php echo url::base();?>images/delete.png" title="Удалить">
                </p>
            </div>
            <?php endforeach;?>
            <form method="post" action="<?php echo url::base()?>ajax/status/1">
                <p>
                    Добавить <input type="text" size="25" name="name">
                    <input type="submit" value="ok">
                </p>
            </form>
            <p>&nbsp;</p>
            <h4 id="ansver2" style="background-color: #F0F0F0; padding: 10px; display: none; cursor: pointer;"></h4>
            <hr><br>
            <b>Голосование</b><br>
            <form method="post" action="">
                <p>
                    Вопрос<br>
                    <textarea cols="30" name="question" rows="3"><?php echo $question;?></textarea><br>
                    Ответы:<br>
                    <textarea cols="30" name="answers" rows="10"><?php foreach($answers as $item):?><?php echo $item->text . "\r\n";?><?php endforeach;?></textarea><br>
                    <input type="submit" value="Сохранить"><br><br>
                    Результаты:<br>
                    <?php foreach($answers as $item):?><span style="color: teal"><?php echo $item->text ;?></span> - <b><?php echo $item->count;?></b><br><?php endforeach;?>
                </p>                   
                <input type="button" value="Обнулить счетчики голосования" onclick="$.get('<?php echo url::base();?>ajax/vote0'); this.value='Успешно!'; $(this).attr('disabled',1)">
                
            </form>
            <br>
            <form action="" method="post"> <input type="submit" name="clearRating" value="Обнулить счетчики рейтинга товаров"></form>

        </td>
    </tr>
</table>
<script type="text/javascript">
$.each($("#userForm").find('input[type=checkbox]'), function(){
    $(this).click(function(){
        $("#submit").trigger('click');
    });
});

$('.saveIt').css('cursor', 'pointer');
$('.removeIt').css('cursor', 'pointer');
$('#ansver').ajaxError(function() {
  $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span>");
  $(this).show("slow");
  $('#submit').attr('disabled', 0);
});
$('#submit').click(function (){
    $(this).attr('disabled', 1);
    $.post('ajax/config',$("#userForm").serialize(),function (data,textStatus){
        $('#ansver').html(data);
        $('#submit').attr('disabled', 0);
        $('#ansver').show("slow");
    });
});
$('#ansver').click(function (){$(this).hide(1000);});
$('#ansver2').click(function (){$(this).hide(1000);});
$('.saveIt').click(function(){
    myP = $(this).parent().children();
    $.post('ajax/status/2',{ id: myP[0].id.split('g')[1], name: $(myP[0]).val() },function (data){
       $('#ansver2').html(data);
       $('#ansver2').show("slow");
    });
});
$('.removeIt').click(function(){
    myP = $(this).parent().children();    
    $.post('ajax/status/3',{ id: myP[0].id.split('g')[1] },function (data){
       $('#ansver2').html(data);
       $('#ansver2').show("slow");
    });
    $(this).parent().hide();
});
$("#datalog").css('display','none');

$('#turnOnInvites').click(function(){$('#turnOnInvites').css('background-color','white');});
</script>

<div id="errorlogplace"></div>
