<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div>
    <div id="votingDiv">
        <div class="module_best2">
            <h3><span><span>Голосование</span></span></h3>

            <div class="boxIndent">
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
                                    {template: 'default2'}, /* template folder name!  */
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
        </div>
    </div>
</div>
<br>