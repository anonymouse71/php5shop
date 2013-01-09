<?php defined('SYSPATH') OR die('No direct access allowed.');?>

<!-- описание товара -->
<div style="width: 550px">
    <?php echo $text;?>
</div>
<!-- /описание товара -->

<?php if(isset($id)): //только для администратора ?>
<a href="admin/description/<?php echo $id;?>"><img style="border: 0" alt="edit" src="images/edit.png" title="Редактировать описание"></a>
<?php endif; ?>

<?php /* * * VK виджеты и комментарии * * */
if($vk_on):?>
<div id="vk_like" style="padding:5px;"></div>
<script type="text/javascript">
VK.Widgets.Like("vk_like", {type: "button"});
</script>

<div id="vk_comments"></div>
<script type="text/javascript">
VK.Widgets.Comments("vk_comments", {limit: 10, width: "496", attach: "*"});
</script>
<?php endif; ?>

<?php /* * * Система удаленных комментариев disqus * * */
if(isset($disqus_shortname)): ?>
<div id="disqus_thread"></div>
<script type="text/javascript">    
    var disqus_shortname = '<?php echo $disqus_shortname;?>';
    var disqus_identifier = '<?php echo $_SERVER['REQUEST_URI'];?>';
    var disqus_url = '<?php echo 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];?>';
    (function() {
        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    })();
</script>
<noscript>Включите JavaScript чтобы видеть комментарии</noscript>
<?php endif; ?>

