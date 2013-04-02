<?php defined('SYSPATH') or die('No direct script access.');?>

<h2>Страница редактирования профиля <?php if(isset($adm))echo '(id'.$user->id.')';?></h2>
<br>
<form action="" method="post" id="userForm">
    <table border="0" <?php if(!isset($adm)):?>width="350px"<?php endif;?>>
        <tr>
            <td><span>Имя (ФИО):</span> </td>
            <td><input class="line" <?php if(!isset($adm)):?>disabled="1"<?php endif;?> type="text" name="username" value="<?php echo htmlspecialchars($user->username);?>"></td>
        </tr>

        <tr>
            <td><span>Email:</span> </td>
            <td><input class="line" type="text" name="email" value="<?php echo htmlspecialchars($user->email);?>"></td>
        </tr>
        <?php if(isset($pct)):?><tr>
            <td><span>Скидка:</span> </td>
            <td><input class="line" disabled="1" type="text" name="pct" value="<?php echo $pct;?>"></td>
        </tr><?php endif;?>
        <tr>
            <td><span>Телефон:</span></td>
            <td><input class="line" type="text" name="phone" value="<?php echo htmlspecialchars($user->phone);?>"></td>
        </tr>
        <?php if(isset($fields) && is_object($fields)): ?>
        <?php foreach($fields as $field):?>
        <tr>
            <td><span><?php echo $field->name;?>:</span></td>
            <td>
                <?php if($field->type < 5): ?><input class="line" type="text" name="f<?php echo $field->id;?>" value="<?php if(isset($fieldVals[$field->id]))echo htmlspecialchars( $fieldVals[$field->id] );?>">
                <?php elseif($field->type == 5):?><input class="line" type="checkbox" name="f<?php echo $field->id;?>" <?php if(isset($fieldVals[$field->id]) && $fieldVals[$field->id])echo 'checked="1"';?>>
                <?php elseif($field->type == 6):?><textarea class="line" cols="18" rows="2" name="f<?php echo $field->id;?>"><?php if(isset($fieldVals[$field->id]))echo htmlspecialchars( $fieldVals[$field->id] );?></textarea>
                <?php endif;?>
            </td>
        </tr>

        <?php endforeach;?>
        <?php endif;?>
        <tr>
            <td><span>Адрес доставки:</span> </td>
            <td><textarea class="line" cols="18" rows="2" name="address"><?php echo nl2br(htmlspecialchars($user->address));?></textarea></td>
        </tr>
        <?php if(isset($adm) && isset($groups)):?>
        <tr>
            <td><span>Группа</span><input style="display:none;" name="pass"></td>
            <td><select name="gid"><?php foreach($groups as $gr):?><option value="<?php echo $gr['id'];?>"><?php echo $gr['name'];?></option><?php endforeach;?></select></td>
        </tr><?php endif;?>
        <?php if(isset($adm) && isset($is_admin)):?>
        <tr>
            <td><span>Администратор</span></td>
            <td><select name="is_admin"><?php if($is_admin):?><option value="1">да</option><option value="2">нет</option><?php else:?><option value="2">нет</option><option value="1">да</option><?php endif;?></select></td>
        </tr><?php endif;?>

        <tr align="center">
            <td>&nbsp;</td>
            <td><input type="button" id="submit" value="сохранить"></td>
        </tr>
    </table>
    <br><br>
    <h4 id="ansver" style="background-color: #F0F0F0; padding: 10px; display: none; cursor: pointer;"></h4>
    <input type="hidden" name="id" value="<?php echo $user->id; ?>">
</form>

<script type="text/javascript">
$('#ansver').ajaxError(function() {
  $(this).html("<span style='color:red;'>Произошла ошибка! проверьте подключение к Internet</span>");
  $(this).show("slow");
  $('#submit').attr('disabled', 0);
});
    
$('#submit').click(function (){
    $(this).attr('disabled', 1);
    $.post('ajax/user',$("#userForm").serialize(),function (data,textStatus){
        $('#ansver').html(data);
        $('#submit').attr('disabled', 0);
        $('#ansver').show("slow");
    });
});

$('#ansver').click(function (){$(this).hide(1000);});

</script>
<?php if(isset($info)): ?>
<p>
    <?php echo $info;?>    
</p>
<?php endif;?>

<?php if (count($views)): ?>
    <style>
        .p_views {
            margin: 10px;
            width: 100%;
            border-bottom: dotted 1px #000000;
            padding-bottom: 10px;
        }
    </style>

    <p>&nbsp;</p>
    <h3>Последние просмотренные товары:</h3>
    <?php foreach ($views as $prod): ?>
        <div class="p_views">
            <?php
            echo '<a href="', url::base(), 'shop/product', $prod['product_id'], '">',
            (file_exists(
                $_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/small/' . $prod['product_id'] . '.jpg')
                ?
                '<img src="' . url::base() . 'images/products/small/' . $prod['product_id'] . '.jpg" alt="" />'
                : '<img src="' . url::base() . 'images/no-photo.jpg" alt="" />'),
            '<br />',
            htmlspecialchars($prod['name']),
            '</a>';
            ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

