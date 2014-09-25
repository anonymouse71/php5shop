<?php defined('SYSPATH') or die('No direct script access.');?>
<form action="" method="post">
    <h2>Вход в панель управления</h2>
    <fieldset>
        <p>
            <label for="username">Логин</label>
            <br>
            <input type="text" name="username" id="username"
                   value="<?php if (isset($_POST['username']))
                       echo htmlspecialchars($_POST['username']); ?>">
        </p>
        <p>
            <label for="password">Пароль</label>
            <br>
            <input type="password" name="password" id="password">
        </p>
        <p>
            <input type="hidden" name="token" value="<?php echo $token ?>">
            <input type="submit" value="Войти">
        </p>
    </fieldset>
</form>
<?php echo $error;