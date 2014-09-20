<?php defined('SYSPATH') or die('No direct script access.');?>
<h2>Ваша корзина</h2>
<?php if(!count($products)):?>пуста
<?php else:?>
<br><br>
<table border="0" class="ordhd">
    <tr>
        <td width="150px;"><b>Название</b></td><td width="20px;">&nbsp;</td><td><b>Цена</b></td><td width="20px;">&nbsp;</td><td><b>Количество</b></td><td width="20px;">&nbsp;</td><td width="20px;">&nbsp;</td><td></td>
    </tr>

<?php foreach($products as $item):?>
    <tr class="post">
        <td><a href="<?php echo Model_Product::getProdUri($item['path']);?>"><?php echo $item['name'];?></a></td>
        <td></td>
        <td><?php echo $item['price'];?></td>
        <td></td>
        <td align="center"><?php echo $item['count'];?></td>
        <td></td>
        <td><img class="imgcart" alt="Убрать из корзины" src="images/x.png" name="<?php echo $item['id'];?>"></td>
    </tr>

<?php endforeach;?>
</table>

<h3 id="sumOrd">Сумма заказа: <?php echo $sum;?></h3>
<br><br>
<a href="order" class="ordhd"><strong>Перейти к покупке</strong></a>
<?php endif;?>

<script type="text/javascript">
    $('.imgcart').click(function(){
        $.post('ajax/delfromcart/' + this.name,null, function(data){
            if(data.sum == 0){
                $("#sumOrd").html(" ");
                $(".ordhd").hide();                
            } else
                if(data.sum.length < 1000)
                    $("#sumOrd").html("Сумма заказа: " + data.sum);
            
        },"json");
        $(this).parent().parent().hide();
        $('#CartItems').html($('#CartItems').html() - 1);
    });
</script>