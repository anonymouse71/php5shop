<?php defined('SYSPATH') or die('No direct script access.'); ?>


<div id="price_filter">
    <img src="images/top_bg.gif" alt="" width="218" height="12">

    <div class="block_div">
        <b>Цена:</b><br>
        <div class="currency"><?php echo $currency ?></div>
        <form action="" method="post">
            от <input type="text" id="price_from" name="price_from" value="<?php echo $min_price ?>" size="7"/>
            до <input type="text" id="price_to" name="price_to" value="<?php echo $max_price ?>" size="12"/>

            <br>
            <div style="text-align: center;margin-top: 4px;"><input type="submit" value="Применить фильтр"></div>

        </form>
        <form action="" method="post" style="text-align: center">
            <input type="submit" value="Отменить фильтр" name="disable_price_filtration">
        </form>
    </div>
    <img src="images/bot_bg.gif" alt="" width="218" height="10">
</div>
