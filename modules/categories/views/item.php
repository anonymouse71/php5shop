<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="level<?php echo  $level;?>">
    <a href="<?php echo $path;?>" class="category-item" title="<?php echo $name;?>">
        <?php
        if(isset($tag))
            echo "<$tag>$name</$tag>";
        else
            echo $name;
        ?>
    </a>
</div>