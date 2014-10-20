<?php defined('SYSPATH') or die('No direct script access.'); ?>

    <img src="images/top_bg.gif" alt="" width="218" height="12">
    <div id="lastNews">
        <?php foreach ($data as $post): ?>

            <div>
                <h3><?php echo htmlspecialchars($post->title);?></h3>
                <?php echo $post->html2;?>
                <div class="right" align="right">
                    <a href="blog/<?php echo $post->id; ?>"><?php echo __('читать...')?></a>
                </div>
            </div>

        <?php endforeach;?>
    </div>
    <img src="themes_public/default1/img/bot_bg.gif" alt="" width="218" height="10"><br>
<?php if (count($data) > 1): ?>
    <script>
        $(function () {
            var maxH = 100;
            $('#lastNews').children().each(function (i, v) {
                if ($(v).height() > maxH)
                    maxH = $(v).height();
            });
            $('#lastNews').slidesjs({
                width: 200,
                height: maxH,
                play: {
                    active: true,
                    auto: true,
                    interval: 15000,
                    swap: true
                }
            });
        });
    </script>
<?php endif;