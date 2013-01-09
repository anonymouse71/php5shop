<?php defined('SYSPATH') or die('No direct script access.');?>

<input type="hidden" id="whs<?php echo $item1['id'];?>" value="<?php echo $item1['whs'];?>"/>
<input type="hidden" id="whs<?php echo $item2['id'];?>" value="<?php echo $item2['whs'];?>"/>

<h3 id="showHeaderCompare" >Сравнение товаров</h3>
<table border="0">
    <tr>
        <td><h2><a href="shop/product<?php echo $item1['id'];?>"><?php echo $item1['name'];?></a></h2></td>
        <td><h2><a href="shop/product<?php echo $item2['id'];?>"><?php echo $item2['name'];?></a></h2></td>
    </tr>    
    <tr>
        <td>
            <div class="item" id="n<?php echo $item1['id'];?>">
    
    <?php $urlImg = 'images/products/small/' . $item1['id'] . '.jpg" class="imgzoom';
          $pathImg = $_SERVER['DOCUMENT_ROOT'].url::base().'images/products/small/'.$item1['id'].'.jpg';?>

    <img align="left" src="<?php echo file_exists($pathImg)? $urlImg : url::base().'images/no-photo.jpg';?>" alt="<?php echo $item1['name'];?>" >
    <div style="display:none">
        <img align="left" src="<?php echo file_exists($_SERVER['DOCUMENT_ROOT'].url::base().'images/products/'.$item1['id'].'.jpg')?'images/products/' . $item1['id'] . '.jpg' : 'images/no-photo.jpg';?>" alt="<?php echo $item1['name'];?>" >
        <div style="display:none"><?php
            $n = 0;
            while(file_exists($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $item1['id'] . '-' . ($n+1) . '.jpg'))
                echo ++$n . ',';
        ?></div>
        <?php if($n): ?>
        <a href="javascript:void(0);" class="nextphoto"><h1>Следующее фото &rarr;</h1></a>
        <div class="nfrom"><span>1</span> из <?php echo ($n+1);?></div>
        <?php endif;?>
    </div>


    <span>
         <?php echo ($item1['whs'])?$item1['price']:'нет на складе';?>
    </span>

    <?php if(file_exists($_SERVER['DOCUMENT_ROOT'].url::base().'images/products/'.$item1['id'].'.jpg')): ?>
    <img src="images/viewmag.png" alt="Масштаб изображения" title="Смотреть фото" class="imgzoom">
    <?php endif;?>

    <?php if($item1['whs'] && !$item1['cart']): ?>
    <a href="javascript:void(0);">
        <img src="images/carts.gif" alt="Добавить в корзину" class="imgcart" title="Добавить в корзину" >
    </a>
    <?php endif;?>

    <?php if(isset($item1['bigcart']) && $item1['cart'] && $item1['whs']): ?>
    <input type="text" size="1" value="<?php echo $item1['bigcart'];?>" class="count">
    <?php elseif(isset($bigcart) && $item1['whs']):?>
    <input type="text" size="1" value="1" class="count hdn">
    <?php endif;?>

    <img alt="loading" src="images/loading.gif" class="hdn load">
    <div id="whsError<?php echo $item1['id'];?>" class="whsError"></div>
</div></td><td><div class="item" id="n<?php echo $item2['id'];?>">

    <?php $urlImg = 'images/products/small/' . $item2['id'] . '.jpg" class="imgzoom';
          $pathImg = $_SERVER['DOCUMENT_ROOT'].url::base().'images/products/small/'.$item2['id'].'.jpg';?>

    <img align="left" src="<?php echo file_exists($pathImg)? $urlImg : url::base().'images/no-photo.jpg';?>" alt="<?php echo $item2['name'];?>" >
    <div style="display:none">
        <img align="left" src="<?php echo file_exists($_SERVER['DOCUMENT_ROOT'].url::base().'images/products/'.$item2['id'].'.jpg')?'images/products/' . $item2['id'] . '.jpg' : 'images/no-photo.jpg';?>" alt="<?php echo $item2['name'];?>" >
        <div style="display:none"><?php
            $n = 0;
            while(file_exists($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $item2['id'] . '-' . ($n+1) . '.jpg'))
                echo ++$n . ',';
        ?></div>
        <?php if($n): ?>
        <a href="javascript:void(0);" class="nextphoto"><h1>Следующее фото &rarr;</h1></a>
        <div class="nfrom"><span>1</span> из <?php echo ($n+1);?></div>
        <?php endif;?>
    </div>


    <span>
         <?php echo ($item2['whs'])?$item2['price']:'нет на складе';?>
    </span>

    <?php if(file_exists($_SERVER['DOCUMENT_ROOT'].url::base().'images/products/'.$item2['id'].'.jpg')): ?>
    <img src="images/viewmag.png" alt="Масштаб изображения" title="Смотреть фото" class="imgzoom">
    <?php endif;?>

    <?php if($item2['whs'] && !$item2['cart']): ?>
    <a href="javascript:void(0);">
        <img src="images/carts.gif" alt="Добавить в корзину" class="imgcart" title="Добавить в корзину" >
    </a>
    <?php endif;?>

    <?php if(isset($item2['bigcart']) && $item2['cart'] && $item2['whs']): ?>
    <input type="text" size="1" value="<?php echo $item2['bigcart'];?>" class="count">
    <?php elseif(isset($bigcart) && $item2['whs']):?>
    <input type="text" size="1" value="1" class="count hdn">
    <?php endif;?>

    <img alt="loading" src="images/loading.gif" class="hdn load">
    <div id="whsError<?php echo $item2['id'];?>" class="whsError"></div>
</div></td>
    </tr>
    <tr>
        <td><?php echo str_replace('<div style="width: 550px">','<div>',$description1);?></td>
        <td><?php echo str_replace('<div style="width: 550px">','<div>',$description2);?></td>
    </tr>
</table>


<script type="text/javascript">
<!--
$('.imgzoom').click(function(){
    var id = $(this).parent().attr('id').split('n')[1];
    var elem = $(this).parent().children()[1];
    $(elem).modal();

    var img = $('.simplemodal-data').children()[0];
    $(img).attr('src',"images/loading.gif");
    $(img).attr('src',"images/products/" + id + ".jpg");
    $('.simplemodal-data').attr('align','center');
    $('#simplemodal-container').css({'width':'507px','height':'507px','top':'150px','left':'20%','position':'fixed'});

});
$('.nextphoto').click(function(){
    var ind = $(this).next().children();
    var numbers = $(this).prev().html().split(',');
    var boolfinish = false;
    var img = $(this).prev().prev();
    var idarray = $(img).attr('src').split('images/products/');
    idarray = idarray[1].split('.jpg');
    var id = idarray[0];
    var n = id.split('-');
    var n2 = 0;
    if(n.length == 2)
    {
        id = parseInt(n[0]);
        n2 = parseInt(n[1]);
    }
    $.each(numbers, function(index, value){
        value = parseInt(value);
        if(value > 0)
            if(n2 < value && boolfinish != true){
                $(img).attr('src',"images/loading.gif");
                $(img).attr('src' , "images/products/" + id + '-' + value + ".jpg" );
                boolfinish = true;
                $(ind[0]).html(value -1 + 2);
            }
    });
    if(boolfinish != true){
        $(img).attr('src',"images/loading.gif");
        $(img).attr('src' , "images/products/" + id + ".jpg");
        $(ind[0]).html(1);
    }

});
$('.imgcart').click(function(){
    var id = $(this).parent().parent().attr('id').split('n')[1];
    $.post('ajax/add_to_cart/' + id);
    $(this).hide(500);
    $('#CartItems').html($('#CartItems').html() -1 + 2);
<?php if(isset($bigcart)):?>
    elems = $(this).parent().parent().children();
    for(var i=0; i<elems.length; i++)if($(elems[i]).attr('class') == 'count hdn') elems[i].style.display = 'block';
<?php endif;?>
});
<?php if(isset($bigcart)):?>

$('.count').keyup(function(){
    var prodId = $(this).parent().attr('id').split('n')[1];
    var whs = $("#whs" + prodId).val();   

    if(parseInt($(this).val()) > parseInt(whs)){
        $(this).val(whs);
        $("#whsError" + prodId).html("Сейчас на складе только " + whs);        
        $("#whsError" + prodId).bind('click',function(){ $(this).html("") });
    }else{
        $("#whsError" + prodId).html("");        
    }    
    $.post('ajax/add_to_cart/' + prodId + '/' + $(this).val());
    elems = $(this).parent().children();
    for(var i=elems.length-1;i>0;i--)if($(elems[i]).attr('alt') =='loading'){elems[i].style.display='block'; break;}

    setTimeout( function(){$(elems[i]).hide()} ,3000);
});

<?php endif;?>

$("#showHeaderCompare").animate({width: "50%",opacity: 0.3,marginLeft: "0.6in",fontSize: "2em"}, 500 );
-->
</script>