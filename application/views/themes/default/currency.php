<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div class="topblock1">
    Валюта:<br>
    <select id="currency">
        <option value="<?php echo $currency;?>"><?php echo $currency;?></option>
        <?php foreach ($array as $curr): ?><?php if ($curr != $currency): ?>
        <option value="<?php echo $curr;?>"><?php echo $curr;?></option>
        <?php endif; ?><?php endforeach;?>
    </select>
</div>
<script type="text/javascript">
    $('#currency').change(function () {
        document.location.href = '<?php echo url::base();?>shop/currency/' + $('#currency').val();
    });
</script>
<br>