<div id="helpCart" style="display: none">
    <!-- Подсказка чтобы помочь пользователю найти корзину -->
    <div style="font-size: 20px">
        Для оформления заказа перейдите по ссылке:<br><br>

    <a href="<?php echo url::base();?>order/cart"><img
            width="24" height="24" src="/themes_public/default2/img/Cart.png"
            alt="Cart icon" title="Товар в корзине"
            />&nbsp;&nbsp;<span>Корзина</span></a>
    </div>

    <div style="font-size: 16px">
        <br><br>
        Если желаете посмотреть другие товары, просто закройте это окно.
        <br><br>
        Вы сможете попасть в корзину по ссылке из верхнего меню.
    </div>


</div>

<div class="topblock2"
     onclick="document.location.href = '<?php echo url::base();?>order/cart'"
     style="cursor: pointer;float: right;z-index: 120;">
    <img src="themes_public/default2/img/cart-43-24.png" alt="корзина" class="shopping" height="24" width="24">
   <span>В корзине </span><span id="CartItems" style="font-weight: bold;"><?php echo $items;?></span>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".imgcart").click(function () {

            if ($(this).attr('src') == "images/x.png")
                return;

            /* show help message */
            $("#helpCart").modal();
        });
    });
</script>