<?php defined('SYSPATH') or die('No direct script access.'); ?>
<section class="tabs vertical">
    <ul class="tab-nav">
        <li>
            <a data-content="poll_tab" href="javascript:void(0);" class="nav_toggle">Голосование</a>
        </li>
    </ul>
    <div class="tab-content" id="poll_tab">
        <b><?php echo $q; ?></b><br>
        <ul style="padding-left: 12px; " type="circle">
            <?php if (!$cookie): ?>
                <?php foreach ($a as $text): ?>
                <li>
                    <a class="vote" id="vote<?php echo $text->id; ?>" href="javascript:void(0);">
                        <?php echo $text->text; ?>
                    </a>
                </li>
            <?php endforeach; ?>
                <script type="text/javascript">
                    $('.vote').click(function () {
                        $.post('ajax/' + $(this).attr('id'),
                            {template: 'mobile1'}, /* template folder name!  */
                            function (data) {
                                $($("#votingDiv").parent()).html(data);
                            }, 'html');
                    });
                </script>
            <?php else: ?>
                <?php foreach ($a as $text): ?>
                    <li>
                        <?php echo $text->text; ?>
                        (<?php if ($count > 0) echo round($text->count / $count * 100); ?>%)

                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</section>

