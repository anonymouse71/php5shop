<?php defined('SYSPATH') or die('No direct script access.'); ?>

<div id="price_filter" class="module_best2">
    <h3><span><span>Цена:</span></span></h3>
    <div class="block_div">
        <div class="currency_p"><?php echo $currency ?></div>
        <form action="" method="post">
            от <input type="text" name="price_from" value="<?php echo $min_price ?>" size="7"/>
            до <input type="text" name="price_to" value="<?php echo $max_price ?>" size="12"/>

            <br>
            <div style="text-align: center;margin-top: 4px;"><input type="submit" value="Применить фильтр"></div>

        </form>
        <form action="" method="post" style="text-align: center">
            <input type="submit" value="Отменить фильтр" name="disable_price_filtration">
        </form>
    </div>
</div>
