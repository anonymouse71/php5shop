<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="left70">
<form id="userForm" action="" method="post">

    <table border="0" width="350px">
        <tr>
            <td><span>Ваше ФИО:</span> </td>
            <td><input class="line" type="text" name="username" value="<?php
                if(isset($val['username']))
                    echo htmlspecialchars($val['username']);?>"
                       autocomplete="name"></td>
        </tr>
        <tr>
            <td><span>Email:</span> </td>
            <td><input class="line" type="text" name="email" autocomplete="email"
                       value="<?php
                       if(isset($val['email']))
                           echo htmlspecialchars($val['email']);?>"></td>
        </tr>
        <tr>
            <td><span>Телефон:</span></td>
            <td><input class="line" type="text" name="phone" autocomplete="tel"
                       value="<?php
                       if(isset($val['phone']))
                           echo htmlspecialchars($val['phone']);?>"></td>
        </tr>
        <?php if (isset($fields) && is_array($fields)): ?>
            <?php foreach ($fields as $field): ?>
                <?php if ($field->type == 5): ?>
                    <tr>
                        <td colspan="2"><label for="f<?php echo $field->id; ?>"><span><?php echo $field->name; ?></span></label>
                            <input class="line" type="checkbox" id="f<?php echo $field->id; ?>"
                                   name="f<?php echo $field->id; ?>" <?php echo (isset($val['f' . $field->id]) && !$val['f' . $field->id]) ? '' : 'checked="1"'; ?>>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td><span><?php echo $field->name; ?>:</span></td>
                        <td>
                            <?php if ($field->type < 5): ?>
                                <input class="line" type="text" name="f<?php echo $field->id; ?>" value="<?php echo $val['f' . $field->id]; ?>">
                            <?php elseif ($field->type == 6): ?>
                                <textarea class="line" cols="18" rows="4" name="f<?php echo $field->id; ?>"><?php echo nl2br(htmlspecialchars($val['f' . $field->id])); ?></textarea>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td><span>Адрес доставки:</span> </td>
            <td><textarea class="line" cols="25" rows="5" name="address"><?php if(isset($val['address'])) echo nl2br(htmlspecialchars($val['address']));?></textarea></td>
        </tr>

    </table>
    <div id="errors" style="color:red;"><?php if(isset($errors)) echo htmlspecialchars($errors);?></div>

</form>
</div>