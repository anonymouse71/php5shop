<div id="helpCart" style="display: none">
    <!-- Подсказка чтобы помочь пользователю найти корзину -->
    <img src="images/arrow-right-down.jpg" alt="" title="Товар в корзине, нажмите на нее" style="float: left" />
    <br>Нажмите на корзину <br>чтобы перейти к <br>оформлению заказа.
</div>

<div class="topblock2" onclick="document.location.href = '<?php echo url::base();?>shop/cart/'" style="cursor: pointer">
    <img src="images/shopping.gif" alt="корзина" class="shopping" height="24" width="24">

    <p>В корзине </p>

    <p id="CartItems" style="font-weight: bold;"><?php echo $items;?></p>
</div>

<script type="text/javascript">
$(".imgcart").click(function () {
    /* scroll up to the top of page */
    $("#toTop").trigger('click');
    /* show help message */
    $("#helpCart").slideDown();
});
</script>