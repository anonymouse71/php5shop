<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div class="currency">
    <div class="moduletable">
        <h3>Валюта:</h3>
        <div class="field">
            <div class="picker">
                <!-- Currency Selector Module -->
                <div id="select-form">
                    <select id="currency">
                        <option selected value="<?php echo $currency; ?>"><?php echo $currency; ?></option>
                        <?php foreach ($array as $curr): if ($curr != $currency): ?>
                            <option value="<?php echo $curr; ?>"><?php echo $curr; ?></option>
                        <?php endif; ?><?php endforeach; ?>
                    </select>
                </div>
            </div>

        </div>
    </div>

</div>

<script type="text/javascript">
    $('#currency').change(function () {
        document.location.href = '<?php echo url::base();?>shop/currency/' + $('#currency').val();
    });
</script>
