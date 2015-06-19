<?php defined('SYSPATH') or die('No direct script access.');?>
<select class="selectCat">    
    <?php
    foreach($cats as $cat):

	    $id = $cat['id'];
	    $item = $cat['label'];
	    ?>
    <option value="<?php echo $id;?>"<?php
    if($id == $selected) echo ' selected="selected"';?>><?php
	    echo $item;?></option>
        <?php
    endforeach;?>
</select>