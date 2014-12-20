<!--comment form-->

<br>
<div>
    <h3>Оставить свой комментарий.</h3><br>
    <form action="" class="commentaryForm" id="commentaryForm" method="post">
        <?php if($auth):?>
        <label for="yourName">Представьтесь:</label>
        <input id="yourName" name="yourName" type="text" value="<?php if(isset($_POST['yourName']))echo htmlspecialchars($_POST['yourName']);?>">

        <div class="commentaryText">
            <span class="warner"><?php if($errors) echo $errors;?></span>

            <label for="comText">Ваш коментарий:</label>
            <textarea rows="3" cols="3" name="comText" id="comText"><?php if(isset($_POST['comText']))echo htmlspecialchars($_POST['comText']);?></textarea>
        <?php if ($captcha):?>
             Символы с картинки: <input id="captcha" type="text" name="captcha"><br>
        <?php endif ?>
        </div>
            <?php echo $captcha;?>
        
        <input value="Отправить" type="button" id="submitCommentForm">
        <?php else: ?>
        Внимание! Перед тем как добавить коментарий необходимо авторизоваться!
        <?php endif; ?>
	
        <input type="hidden" id="rate" name="rate" value="">
    </form>
</div>
<script type="text/javascript">
    $('#submitCommentForm').click(function(){
        $("#rate").val($('.star-rating-on.star-rating-live').length);
        $('#commentaryForm').submit();
    });

</script>
<!--/comment form-->
