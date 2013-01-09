<style type="text/css">
    .newsCommentary{
        border: 1px solid #f0f0f0;
        overflow: hidden;
        padding: 13px 27px;
        font-family:Arial, Helvetica, sans-serif;
        margin-bottom: 24px;
    }
    .newsCommentary h3{
        margin: 0 0 4px -20px;
        font-size:18px;
    }


</style>
<div class="newsCommentary">

    <h3>Коментарии:</h3>
    <ul class="commentaryList">
        <?php foreach ($data as $comment): ?>
            <li id="comment<?php echo $comment->id; ?>">
                <span class="title"><?php echo htmlspecialchars($comment->username); ?>:</span>
                <?php if($admin) :?>
                    <div style="float:right; padding-left:5px;">
                        <a href="<?php echo url::base();?>admin/deletecomment/<?php echo $comment->id;?>">
                            <img alt="Удалить комментарий" src="images/delete.png" title="Удалить комментарий" style="cursor: pointer;">
                        </a>
                        <i>(Пользователь
                        <a href="<?php echo url::base();?>admin/user/<?php echo $comment->user;?>">id<?php echo $comment->user;?></a>
                        )</i>
                    </div>
                <?php endif;?>

            <div><?php echo nl2br(htmlspecialchars($comment->text)); ?></div>
            <?php if($rate) :?>
            <br>
            <div class="raiting" style="float:right;">
                <div style="float:left"> Оценка: &nbsp;&nbsp; </div>
              
                    <div class="star-rating <?php if($comment->rate >= 1) echo 'star-rating-on';?>">
                        <a title="1">1</a>
                    </div>
                    <div class="star-rating <?php if($comment->rate >= 2) echo 'star-rating-on';?>">
                        <a title="2">2</a>
                    </div>
                    <div class="star-rating <?php if($comment->rate >= 3) echo 'star-rating-on';?>">
                        <a title="3">3</a></div>
                    <div class="star-rating <?php if($comment->rate >= 4) echo 'star-rating-on';?>">
                        <a title="4">4</a>
                    </div>
                    <div class="star-rating <?php if($comment->rate == 5) echo 'star-rating-on';?>">
                        <a title="5">5</a>
                    </div>
            </div>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</div>