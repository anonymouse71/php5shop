<div id="helpCart" style="display: none">
    <!-- Подсказка чтобы помочь пользователю найти корзину -->
    <img src="images/arrow-right-down.jpg" alt="" title="Товар в корзине, нажмите на нее" style="float: left" />
    <br>Нажмите на корзину <br>чтобы перейти к <br>оформлению заказа.
</div>

<div class="topblock2"
     onclick="document.location.href = '<?php echo url::base();?>order/cart'"
     style="cursor: pointer;float: right;z-index: 120;">
    <img src="images/default2/cart-43-24.png" alt="корзина" class="shopping" height="24" width="24">
   <span>В корзине </span><span id="CartItems" style="font-weight: bold;"><?php echo $items;?></span>
</div>

<script type="text/javascript">
$(".imgcart").click(function () {

    if($(this).attr('src') == "images/x.png")
        return;

    /* scroll up to the top of page */
    $("#toTop").trigger('click');
    /* show help message */
    $("#helpCart").slideDown();
});
</script>