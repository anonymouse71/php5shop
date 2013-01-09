<?php defined('SYSPATH') or die('No direct script access.'); ?>
<!-- modal content -->
<div id="modal-content">
    <?php if ($ok): ?>
        <h1>Регистрация успешно завершена! </h1>
        <br>
        <p>Вы можете авторизоваться используя логин и пароль, который указали при регистрации.</p><br>
        
    <?php else: ?>
        <h1>Регистрация еще не завершена! </h1>
        <br>
        <p>Произошла ошибка. Проверьте правильно ли скопировали ссылку из письма. 
            <br><br>
            В случае проблем, попробуйте пройти регистрацию повторно или обратится к администрации. 
        </p><br>
    <?php endif; ?>
</div>
<script type="text/javascript">$('#modal-content').modal();</script>