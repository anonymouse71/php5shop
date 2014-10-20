<?php defined('SYSPATH') or die('No direct script access.'); ?>
<script src='js/jquery.rating.js' type="text/javascript"></script>
<style type="text/css">
    div.rating-cancel, div.star-rating {
        float: left;
        width: 17px;
        height: 15px;
        text-indent: -999em;
        cursor: pointer;
        display: block;
        background: transparent;
        overflow: hidden
    }

    div.star-rating, div.star-rating a {
        background: url(images/star.gif) no-repeat 0 0px
    }

    div.rating-cancel a, div.star-rating a {
        display: block;
        width: 16px;
        height: 100%;
        background-position: 0 0px;
        border: 0
    }

    div.star-rating-on a {
        background-position: 0 -16px !important
    }

    div.star-rating-hover a {
        background-position: 0 -32px
    }

    div.star-rating-readonly a {
        cursor: default !important
    }

    div.star-rating {
        background: transparent !important;
        overflow: hidden !important
    }
</style>
<div style="">
    <input type="radio" class="star" value="1" <?php if ($val == 1) echo 'checked="checked"'; ?>>
    <input type="radio" class="star" value="2" <?php if ($val == 2) echo 'checked="checked"'; ?>>
    <input type="radio" class="star" value="3" <?php if ($val == 3) echo 'checked="checked"'; ?>>
    <input type="radio" class="star" value="4" <?php if ($val == 4) echo 'checked="checked"'; ?>>
    <input type="radio" class="star" value="5" <?php if ($val == 5) echo 'checked="checked"'; ?>>
    &nbsp;
</div>
<script type="text/javascript">
    <?php if($disable):?>
    $('.star').attr('disabled', 1);
    <?php endif;?>
    $('.star').rating({
        callback: function (value, link) {
            $(this).attr('disabled', 1);
            $.get("ajax/rating/" + value);

        }
    });
</script>
