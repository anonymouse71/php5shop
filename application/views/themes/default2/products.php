<?php defined('SYSPATH') or die('No direct script access.');
$count_all = count($products);
$td = 0;
?>

<div class="module-new">
<div class="wrapper" style="position:relative;">
<div class="boxIndent">
<div class="wrapper" style="position:relative;">

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <?php
    for ($i = 0; $i < $count_all; $i++):
        $item = $products[$i];
        if ($td == 0)
            echo '<tr class="first">';
    ?>
    <td width="50%" class="hLine">
        <div class="featuredIndent item" id="n<?php echo $item['id'];?>">
            <div class="featuredborder">
                <div class="product_name">
                    <a href="<?php echo 'shop/product' . $item['id'] ?>" class="name product_name">
                        <h4><?php echo $item['name']; ?></h4>
                    </a>
                </div>
                <?php $urlImg = url::base() . 'images/products/small/' . $item['id']
                    . '.jpg" onclick="imgzoom(' . $item['id'] . ');';
                $pathImg = $_SERVER['DOCUMENT_ROOT'].url::base().'images/products/small/'.$item['id'].'.jpg';?>
                <div class="product_image_container">
                <img align="left" src="<?php echo file_exists($pathImg)? $urlImg : 'images/no-photo.jpg';?>" alt="<?php echo $item['name'];?>" >
                </div>
                <div style="display:none" id="modal_img<?php echo $item['id']; ?>">
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
                <div class="box_product_price">
                    <span class="productPrice"><?php echo ($item['whs'])?$item['price']:'нет на складе';?></span>

                    <br/>
                </div>

                <div class="bg">
                    <div class="product-options">

                        <div class="product_details_container">
                            <a class="details" title="<?php echo $item['name']; ?>" href="<?php echo 'shop/product' . $item['id'] ?>">Подробнее</a>
                        </div>



                    </div>
                </div>


            </div></div></td>

    <?php
    if($td == 1)
    {
        $td = 0;
        echo '</tr>';
    }else
        $td++;

    endfor;


    if ($count_all % 2)
        echo '<td></td></tr>';


    ?>
</table>
</div>
</div>
</div>
</div>


<script type="text/javascript">
<!--

function imgzoom(i){

    var elem = $('#modal_img' + i);
    $(elem).modal();
    var img = $('.simplemodal-data').children()[0];
    $(img).attr('src',"<?php echo url::base();?>images/loading.gif");
    $(img).attr('src',"<?php echo url::base();?>images/products/" + i + ".jpg");
    $('.simplemodal-data').attr('align','center');
    $('#simplemodal-container').css({'width':'507px','height':'507px','top':'150px','left':'20%','position':'fixed'});
}

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



-->
</script>