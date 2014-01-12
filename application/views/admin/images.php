<?php defined('SYSPATH') or die('No direct script access.'); ?>

<script>
    $("nav.navbar-default").hide();
    
    function bigSmall(a,big,small){
        var host = '<?php echo 'http://' . $_SERVER['HTTP_HOST'];?>';
        big = host + big;
        small = host + small;        
        if(big.length == a.children[0].src.length)
            a.children[0].src = small;
        else
            a.children[0].src = big;
    }
</script>


<form action="" method="post" ENCTYPE="multipart/form-data">
    <h2>Загрузить фото для '<?php echo htmlspecialchars($product['name']);?>'</h2>
    <p>        
        <input type="file" name="img">
        <input type="submit" value="Добавить">
    </p>
</form>

<?php if(count($files)):?>
    <?php foreach($files as $i => $a):?>
<div >
    <a href="javascript:void(0);" onclick="bigSmall(this,'<?php echo $a[0];?>','<?php echo $a[1];?>');">
        <img src="<?php echo 'http://' . $_SERVER['HTTP_HOST'], $a[1];?>" alt="Изображение">        
    </a>
    <br>
    <form action="" method="post" >
        <input type="hidden" name="deletePic" value="<?php echo $i;?>">
        <input type="submit" value="Удалить">
    </form>
    <br>
</div>
<br>

    <?php endforeach;?>

<?php endif; ?>
