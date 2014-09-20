<?php defined('SYSPATH') or die('No direct script access.');?>

<?php foreach($products as $item):?>
<input type="hidden" id="whs<?php echo $item['id'];?>" value="<?php echo $item['whs'];?>"/>
<div class="item" id="n<?php echo $item['id'];?>">
    
    <?php $urlImg = url::base() . 'images/products/small/' . $item['id'] . '.jpg" class="imgzoom';
          $pathImg = $_SERVER['DOCUMENT_ROOT'].url::base().'images/products/small/'.$item['id'].'.jpg';?>
    
    <img align="left" src="<?php echo file_exists($pathImg)? $urlImg : 'images/no-photo.jpg';?>" alt="<?php echo $item['name'];?>" >
    <div style="display:none">
        <img align="center" src="<?php echo file_exists($_SERVER['DOCUMENT_ROOT'].url::base().'images/products/'.$item['id'].'.jpg')?'images/products/' . $item['id'] . '.jpg' : 'images/no-photo.jpg';?>" alt="<?php echo $item['name'];?>" >
        <div style="display:none"><?php
            $n = 0;
            while(file_exists($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $item['id'] . '-' . (1+$n) . '.jpg'))
                echo ++$n . ',';
        ?></div>
        <?php if($n): ?>
        <a href="javascript:void(0);" class="nextphoto"><h1>Следующее фото &rarr;</h1></a>
        <div class="nfrom"><span>1</span> из <?php echo ($n+1);?></div>
        <?php endif;?>
    </div>
  
    <a href="<?php echo Model_Product::getProdUri($item['path'])?>" class="name">
            <?php echo $item['name'];?>
    </a>

    <span>
         <?php echo ($item['whs'])?$item['price']:'нет на складе';?>
    </span>

    <?php if(file_exists($_SERVER['DOCUMENT_ROOT'].url::base().'images/products/'.$item['id'].'.jpg')): ?>
    <img src="<?php echo url::base();?>images/viewmag.png" alt="Масштаб изображения" title="Смотреть фото" class="imgzoom">
    <?php endif;?>

    <?php if ($item['whs']): ?>
        <a href="javascript:void(0);" class="imgcart <?php if ($item['cart']) echo 'hdn' ?>" title="Добавить в корзину">
            <img src="images/carts.gif" alt="Добавить в корзину" title="Добавить в корзину">
        </a>

        <?php if ($item['cart'] && isset($item['bigcart'])): ?>
            <input type="text" size="1" value="<?php echo $item['bigcart']; ?>" class="count">
        <?php else: ?>
            <input type="text" size="1" value="1" class="count hdn">
        <?php endif; ?>

        <img alt="loading" src="images/loading.gif" class="hdn load">

        <div id="whsError<?php echo $item['id']; ?>" class="whsError"></div>
    <?php endif; ?>

    <link itemprop="itemCondition" href="http://schema.org/NewCondition" />
    <span style="display: none">ID: <span itemprop="productID"><?php echo $item['id']?></span></span>

</div>
<?php endforeach;?>

<script type="text/javascript">
<!--
$('.imgzoom').click(function(){
    var id = $(this).parent().attr('id').split('n')[1];
    var elem = $(this).parent().children()[1];
    $(elem).modal();
    var img = $('.simplemodal-data').children()[0];
    $(img).attr('src',"<?php echo url::base();?>images/loading.gif");
    $(img).attr('src',"<?php echo url::base();?>images/products/" + id + ".jpg");
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
                $(img).attr('src',"<?php echo url::base();?>images/loading.gif");
                $(img).attr('src' , "<?php echo url::base();?>images/products/" + id + '-' + value + ".jpg" );
                boolfinish = true;
                $(ind[0]).html(value -1 + 2);
            }
    });
    if(boolfinish != true){
        $(img).attr('src',"<?php echo url::base();?>images/loading.gif");
        $(img).attr('src' , "<?php echo url::base();?>images/products/" + id + ".jpg");
        $(ind[0]).html(1);
    }
       
});
$('.imgcart').click(function(){
    var id = $(this).parents('.item').attr('id').split('n')[1];
    $.post('ajax/add_to_cart/' + id);
    $(this).hide();
    $('#CartItems').html($('#CartItems').html() -1 + 2);
    $(this).next().show().val('1');
});

$('.count').keyup(function(){
    var t = $(this);
    var prodId = t.parents('.item').attr('id').split('n')[1];
    var whs = $("#whs" + prodId).val();
    if(t.val().length == 0){
        return;
    }
    var user_want_count = parseInt(t.val());

    if (user_want_count > parseInt(whs)) {
        t.val(whs);
        user_want_count = whs;
        $("#whsError" + prodId).html("Сейчас на складе только " + whs);
        $("#whsError" + prodId).bind('click',function(){ $(this).html("") });
    }else{
        $("#whsError" + prodId).html("");
    }
    $.post('ajax/add_to_cart/' + prodId + '/' + user_want_count);
    var elems = t.parent().children();
    for (var i = elems.length - 1; i > 0; i--)
        if ($(elems[i]).attr('alt') == 'loading') {
            elems[i].style.display = 'block';
            break;
        }
    setTimeout( function(){
        $(elems[i]).hide();
        if(user_want_count == 0){
            var CartItems = parseInt($('#CartItems').html());
            if(CartItems > 0){
                $('#CartItems').html(CartItems -1);
            }
            t.prev().css('display', 'block');
            t.hide();
        }
    } ,1500);
});
-->
</script>