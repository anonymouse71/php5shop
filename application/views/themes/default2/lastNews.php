<?php defined('SYSPATH') or die('No direct script access.'); ?>

    <div class="module_best2">
        <h3><span><span>Последние новости</span></span></h3>

        <div class="boxIndent">
            <div id="lastNews">
                <?php foreach ($data as $post): ?>
                    <div>
                        <?php echo htmlspecialchars($post->title); ?>
                        <?php echo $post->html2; ?>
                        <div class="right">
                            <a href="blog/<?php echo $post->id; ?>"><?php echo __('читать...') ?></a>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>


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