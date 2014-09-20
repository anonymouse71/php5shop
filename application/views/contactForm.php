<?php defined('SYSPATH') or die('No direct script access.');?>

<form action="" method="post" id="contact_form">
    <h2>Форма обратной связи</h2>
    <?php foreach ($data as $row): ?>
        <div>
            <label for="<?php echo $row['name']?>"><?php echo $row['label']?></label>
            <br>
            <input type="text" id="<?php echo $row['name']?>" name="<?php echo $row['name']?>" <?php if($row['required'])
                echo 'required="required"'?> value="<?php echo $row['value']?>">
        </div>
    <?php endforeach;?>
    <br>
    <p><textarea rows="6" cols="50" required="required" name="text" id="text"></textarea></p>

    <p style="min-height: 55px"><?php echo Captcha::instance() ?>
        <input type="text" id="captcha" name="captcha" required="required" autocomplete="off">
        <input type="submit" value="Отправить" id="submit_contact_form">
    </p>
</form>
<p id="contact_send" style="display: none;">Сообщение отправлено!</p>

<script src="<?php echo url::base() ?>js/contact.js"></script>