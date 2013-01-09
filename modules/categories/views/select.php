<?php defined('SYSPATH') or die('No direct script access.');?>
<select class="selectCat">    
    <?php foreach($cats as $id => $item):?>
        <?php if($id == $selected):?>
    <option id="<?php echo $id;?>"><?php echo $item;?></option>
        <?php endif;?>
    <?php endforeach;?>
    <?php foreach($cats as $id => $item):?>
        <?php if($id != $selected):?>
    <option id="<?php echo $id;?>"><?php echo $item;?></option>
        <?php endif;?>
    <?php endforeach;?>
</select>