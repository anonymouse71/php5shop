<?php defined('SYSPATH') or die('No direct script access.');?>
<div style="float: left">
<form id="loginForm" action="login/exit" method="post">
    Удачных покупок, <?php echo @$user;?>.
    <a href="javascript:void(0);" onclick="$('#loginForm').submit();">Выход</a>
    <input type="hidden" name="exit" value="1">
</form>

</div>