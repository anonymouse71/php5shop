<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="post">
    <h2>
        <a href="blog/<?php echo $post->id;?>"><?php echo $post->title;?></a>
    </h2>
    <div class="content">
        <?php echo $post->html;?>
    </div>
    <div class="date">
        <?php echo date('d.m.Y', $post->date);?>
    
    <?php if($is_admin):?>
        <form action="admin/blog/<?php echo $post->id;?>" method="post"><input type="hidden" name="edit" value="Редактировать"></form>
        <form action="admin/blog/<?php echo $post->id;?>" method="post"><input type="hidden" name="remove" value="Удалить"></form>
        <img alt="Редактировать" src="images/edit.png" title="Редактировать" style="cursor: pointer" onclick="$(this).parent().children()[0].submit();">
        &nbsp;&nbsp;
        <img alt="Удалить" src="images/delete.png" title="Удалить" style="cursor: pointer" onclick="$(this).parent().children()[1].submit();">
    <?php endif;?>
    </div>
</div>
<br>
