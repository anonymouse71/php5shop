<?php defined('SYSPATH') or die('No direct script access.');?>
<img src="images/top_bg.gif" alt="" width="218" height="12">
<div style="padding: 10px;">
    <b><?php echo $q;?></b><br>
    <ul style="padding-left: 12px; " type="circle" >
<?php if(!$cookie):?>
    <?php foreach ($a as $text): ?>
        <li>
            <a class="vote" id="vote<?php echo $text->id;?>" href="javascript:void(0);">
                <?php echo $text->text;?>
            </a>
        </li>
    <?php endforeach; ?>
<script type="text/javascript">
$('.vote').click(function(){
    $.post('ajax/' + $(this).attr('id'));
    document.location.href = document.location.href + '';
});
</script>
<?php else: ?>
    <?php foreach ($a as $text): ?>
        <li>
            <?php echo $text->text;?> (<?php if($count >0) echo round($text->count/$count * 100);?>%)
            
        </li>
    <?php endforeach; ?>
<?php endif; ?>
</ul>
</div>
<img src="images/bot_bg.gif" alt="" width="218" height="10"><br>