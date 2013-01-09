<?php defined('SYSPATH') or die('No direct script access.');?>
<img src="images/top_bg.gif" alt="" width="218" height="12">
<form id="loginForm" action="login/exit" method="post">
    <p class="line center pad20">Удачных покупок, <?php echo @$user;?></p>
    <input type="hidden" name="exit" value="1">
    <p class="line center pad20">
        <a href="javascript:void(0);"><img src="images/exit.gif" alt="Выход" height="25" width="69" onclick="document.getElementById('loginForm').submit();" ></a>
    </p>
</form>
<img src="images/bot_bg.gif" alt="" width="218" height="10"><br>
