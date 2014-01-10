<!--comment form-->
<style type="text/css">
    .commentaryList{
        border-top: 1px solid #f0f0f0;
    }
    .commentaryList li{
        border-bottom: 1px solid #f0f0f0;
        padding: 12px 0;
        overflow: hidden;
    }
    .commentaryList li .Ulogin{
        display: block;
        font-size:14px;
        padding-bottom: 4px;
    }
    .commentaryList li .Ulogin a{
        color:#000;
        text-decoration:none;
    }
    .commentaryList li .Ulogin a:hover{
        text-decoration: underline;
    }
    .commentaryList li .title{
        display: block;
        padding-bottom: 4px;
        font-size:14px;
    }
    .commentaryList li .raiting{
        display: block;
        padding-top: 7px;
    }
    .commentaryList li .raiting .rate,
    .commentaryForm .rate{
        display: inline-block;
       
        vertical-align: top;
        position:relative;
        top: -1px;
    }
    .commentaryForm{
        clear: both;
    }
    .commentaryForm label{
        padding-right: 7px;
    }
    .commentaryForm input[type=text]{
        border: 1px solid #f0f0f0;
        padding: 2px 5px;
        color:#383838;
        background:#fff;
    }
    .commentaryText{
        overflow: hidden;
     
        padding-top:3px;
    }
    .commentaryText .warner{
        float: right;
        color:#000;
        padding-top:1px;
        font-size:12px;
    }
    .commentaryForm textarea{
        border: 1px solid #f0f0f0;
        padding: 2px 5px;
        color:#383838;
        background:#fff;
        clear: both;
        display: block;
        width: 500px;
        height: 126px;
        resize: none;
        margin: 3px 0 8px;
        overflow: auto;
    }
    .commentaryForm input[type=button]{
        float: right;

    }
    #submitCommentForm {
        padding: 5px;
    }
</style>
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
        
             Символы с картинки: <input id="captcha" type="text" name="captcha"><br>            
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
