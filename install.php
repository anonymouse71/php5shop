<?php defined('SYSPATH') or exit('Install tests must be loaded from within index.php!');

//функция для замены значений в конфигурационном файле БД
function replaseIt($string, $name, $value)
{
    $name = str_replace("'", "\'", strip_tags($name));
    $value = str_replace("'", "\'", strip_tags($value));
    return preg_replace("#'$name'\s*=>\s*'[^']*',#", "'$name'    =>    '$value',", $string, 1);
}

//если отправлен запрос на редактирование конфигурации БД
if (isset($_POST['hostname'], $_POST['username'], $_POST['password'], $_POST['database']))
{
    $file = file_get_contents('modules/database/config/database.php');
    $file = replaseIt($file, 'hostname', $_POST['hostname']);
    $file = replaseIt($file, 'username', $_POST['username']);
    $file = replaseIt($file, 'password', $_POST['password']);
    $file = replaseIt($file, 'database', $_POST['database']);
    //сохраняем настройки
    file_put_contents('modules/database/config/database.php', $file);
    //если установлен чекбокс "Создать в базе структуру таблиц"
    if (isset($_POST['importSQL']) && file_exists('sql.txt'))
    {
	    $sql = explode(";\n", preg_replace('|(--[^\n]*\n)|', '', file_get_contents('sql.txt')));
		$first_q = "SET NAMES 'utf8';";
	    if (function_exists('mysqli_connect'))
		{
			$dbh = mysqli_connect($_POST['hostname'], $_POST['username'], $_POST['password']) or die('Не могу соединиться с MySQL.');
			mysqli_select_db($dbh, $_POST['database']) or die('Не могу подключиться к базе ' . htmlspecialchars($_POST['database']));
			mysqli_query($dbh, $first_q);
			foreach ($sql as $key => $val)
				if ($val && !mysqli_query($dbh, $val)
				    && 'Query was empty' !== mysqli_error($dbh))
				{
					mysqli_close($dbh);
					die(mysqli_error($dbh) . '<br>' . $val);
				}
			mysqli_close($dbh);
			$file = file_get_contents('modules/database/config/database.php');
			$file = replaseIt($file, 'type', 'mysqli');
			file_put_contents('modules/database/config/database.php', $file);
		}
	    elseif(function_exists('mysql_connect'))
	    {
		    $dbh = mysql_connect($_POST['hostname'], $_POST['username'], $_POST['password']) or die('Не могу соединиться с MySQL.');
		    mysql_select_db($_POST['database']) or die('Не могу подключиться к базе ' . htmlspecialchars($_POST['database']));
		    mysql_query($first_q);
		    foreach ($sql as $key => $val)
			    if ($val && !mysql_query($val) && 'Query was empty' !== mysql_error())
			    {
				    mysql_close($dbh);
				    die(mysql_error() . '<br>' . $val);
			    }
		    mysql_close($dbh);
	    }
	    else
		    die('Not found mysqli or mysql PHP extension');


    }
	@rename('install.php', 'install.php_1');
	header('Location: ' . $_SERVER['REQUEST_URI']);
	exit;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Установка</title>

	<style type="text/css">
	body { width: 42em; margin: 0 auto; font-family: sans-serif; background: #fff; font-size: 1em; background-image:url(images/php5shopLogo.png); background-repeat: no-repeat;}
	h1 { letter-spacing: -0.04em; }
	h1 + p { margin: 0 0 2em; color: #333; font-size: 90%; font-style: italic; }
	code { font-family: monaco, monospace; }
	table { border-collapse: collapse; width: 100%; }
		table th,
		table td { padding: 0.4em; text-align: left; vertical-align: top; }
		table th { width: 12em; font-weight: normal; }
		table tr:nth-child(odd) { background: #eee; }
		table td.pass { color: #191; }
		table td.fail { color: #911; }
	#results { padding: 0.8em; color: #fff; font-size: 1.5em; }
	#results.pass { background: #191; }
	#results.fail { background: #911; }
	</style>

</head>
<body>
	<h1>Проверка конфигурации сервера</h1>
	<p>
		Проверка на совместимость.
	</p>

	<?php $failed = FALSE ?>

	<table cellspacing="0">
		<tr>
			<th>Версия PHP</th>
			<?php if (version_compare(PHP_VERSION, '5.2.3', '>=')): ?>
				<td class="pass"><?php echo PHP_VERSION ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Необходима версия PHP 5.2.3 или новее, а на сервере версия <?php echo PHP_VERSION ?>. Работа на этой версии невозможна!</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Каталог System</th>
			<?php if (is_dir(SYSPATH) AND is_file(SYSPATH.'classes/kohana'.EXT)): ?>
				<td class="pass"><?php echo SYSPATH ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">не существует или не содержит файлов</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Каталог Application</th>
			<?php if (is_dir(APPPATH) AND is_file(APPPATH.'bootstrap'.EXT)): ?>
				<td class="pass"><?php echo APPPATH ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail">не существует или не содержит файлов</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Каталог для кэширования (Cache)</th>
			<?php if (is_dir(APPPATH) AND is_dir(APPPATH.'cache') AND (is_writable(APPPATH.'cache') OR @chmod(APPPATH.'cache',0777)) ): ?>
				<td class="pass"><?php echo APPPATH.'cache' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><code><?php echo APPPATH.'cache' ?></code> - каталог не доступен для записи. Установите права 777</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Каталог sessions</th>
			<?php if (is_dir(APPPATH) AND is_dir(APPPATH.'sessions') AND (is_writable(APPPATH.'sessions') OR @chmod(APPPATH.'sessions',0777)) ): ?>
				<td class="pass"><?php echo APPPATH.'sessions' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><code><?php echo APPPATH.'sessions' ?></code> - каталог не доступен для записи. Установите права 777</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Каталог для кэширования запросов к mySQL</th>
			<?php @mkdir(APPPATH . 'cache/.kohana_cache', 0777);
                              if (is_dir(APPPATH) AND is_dir(APPPATH.'cache/.kohana_cache') AND (is_writable(APPPATH.'cache/.kohana_cache')OR @chmod(APPPATH.'cache/.kohana_cache',0777))): ?>
				<td class="pass"><?php echo APPPATH.'cache/.kohana_cache' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><code><?php echo APPPATH.'cache/.kohana_cache' ?></code> - каталог не доступен для записи. Установите права 777</td>
			<?php endif ?>
		</tr>
        <tr>
			<th>Каталог для сохранения изображений</th>
			<?php if (is_dir(DOCROOT.'images/products/small') AND is_dir(DOCROOT.'images/products') AND (is_writable(DOCROOT.'images/products') OR @chmod(DOCROOT.'images/products',0777))): ?>
				<td class="pass"><?php echo DOCROOT;?>images/products</td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><code>images/products</code> - каталог не доступен для записи. Установите права 777</td>
			<?php endif ?>
		</tr>

        <tr>
            <th>Каталог для дополнительных изображений</th>
            <?php if (is_dir(DOCROOT.'user-img') AND (is_writable(DOCROOT.'user-img') OR @chmod(DOCROOT.'user-img',0777))): ?>
                <td class="pass"><?php echo DOCROOT;?>user-img</td>
            <?php else: $failed = TRUE ?>
                <td class="fail"><code>user-img</code> - каталог не доступен для записи. Установите права 777</td>
            <?php endif ?>
        </tr>
                
		<tr>
			<th>Каталог для записи ошибок (Logs)</th>
			<?php if (is_dir(APPPATH) AND is_dir(APPPATH.'logs') AND (is_writable(APPPATH.'logs') OR @chmod(APPPATH.'logs',0777))): ?>
				<td class="pass"><?php echo APPPATH.'logs/' ?></td>
			<?php else: $failed = TRUE ?>
				<td class="fail"><code><?php echo APPPATH.'logs/' ?></code> - каталог не доступен для записи. Установите права 777</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>PCRE</th>
			<?php if ( ! @preg_match('/^.$/u', 'ñ')): $failed = TRUE ?>
				<td class="fail">Библиотека <a href="http://php.net/pcre">PCRE</a> скомпилирована без поддержки кодировки UTF-8</td>
			<?php elseif ( ! @preg_match('/^\pL$/u', 'ñ')): $failed = TRUE ?>
				<td class="fail">Библиотека <a href="http://php.net/pcre">PCRE</a> скомпилирована без поддержки кодировки UTF-8</td>
			<?php else: ?>
				<td class="pass">ок</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>SPL</th>
			<?php if (function_exists('spl_autoload_register')): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Библиотека PHP <a href="http://www.php.net/spl">SPL</a> отсутствует.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Reflection</th>
			<?php if (class_exists('ReflectionClass')): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Расширение PHP <a href="http://www.php.net/reflection">reflection</a> отсутствует.</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Расширение filter</th>
			<?php if (function_exists('filter_list')): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Расширение <a href="http://www.php.net/filter">filter</a> не загружено</td>
			<?php endif ?>
		</tr>
		<tr>
			<th>Расширение Iconv</th>
			<?php if (extension_loaded('iconv')): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Расширение <a href="http://php.net/iconv">iconv</a> не загружено</td>
			<?php endif ?>
		</tr>
		<?php if (extension_loaded('mbstring')): ?>
		<tr>
			<th>Расширение Mbstring</th>
			<?php if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING): $failed = TRUE ?>
				<td class="fail">Расширение <a href="http://php.net/mbstring">mbstring</a> перегружает стандартные строковые PHP ф-и</td>
			<?php else: ?>
				<td class="pass">ок</td>
			<?php endif ?>
		</tr>
		<?php endif ?>
		<tr>
			<th>URI Determination</th>
			<?php if (isset($_SERVER['REQUEST_URI']) OR isset($_SERVER['PHP_SELF']) OR isset($_SERVER['PATH_INFO'])): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Ни один из <code>$_SERVER['REQUEST_URI']</code>, <code>$_SERVER['PHP_SELF']</code>, или <code>$_SERVER['PATH_INFO']</code> не доступен.</td>
			<?php endif ?>
		</tr>
                <tr>
			<th>Максимальное время выполнения</th>
                        <?php $time = ini_get('max_execution_time');?>
			<?php if ($time >= 180 || !$time ): ?>
				<td class="pass">ок</td>
			<?php else:  ?>
				<td class="fail">Установите в php.ini параметр max_execution_time в 0 или не меньше 180. Сейчас <?php echo $time;?></td>
			<?php endif ?>
		</tr>
                <tr>
			<th>GD Enabled</th>
			<?php if (function_exists('gd_info')): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Расширение  <a href="http://php.net/gd">GD</a> версии 2 не загружено.</td>
			<?php endif ?>
		</tr>
                <tr>
			<th>rss.xml</th>
			<?php if (is_writable('rss.xml') OR @chmod('rss.xml',0777)): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Файл rss.xml не доступен для записи.</td>
			<?php endif ?>
		</tr>
                <tr>
			<th>sitemap.xml</th>
			<?php if (is_writable('sitemap.xml') OR @chmod('sitemap.xml',0777)): ?>
				<td class="pass">ок</td>
			<?php else:  $failed = TRUE ?>
				<td class="fail">Файл sitemap.xml не доступен для записи.</td>
			<?php endif ?>
		</tr>
                <tr>
			<th>cURL</th>
			<?php if (extension_loaded('curl')): ?>
				<td class="pass">ок</td>
			<?php else: $failed = TRUE ?>
				<td class="fail">Расширение <a href="http://php.net/curl">cURL</a> не загружено.</td>
			<?php endif ?>
		</tr>
            <?php if(function_exists('apache_get_modules')) if(!in_array('mod_rewrite',apache_get_modules())):?>
            <?php $failed = TRUE;?>
            <tr>
			<th>mod_rewrite</th>
                        <td class="fail">Модуль Apache mod_rewrite не найден!</td>
             </tr>
            <?php endif ?>

	</table>

	<?php if ($failed === TRUE): ?>
		<p id="results" class="fail">✘ Если не исправить ошибки, правильная работа сайта маловероятна!</p>
	<?php else: ?>
		<p id="results" class="pass">✔ Сервер подходит для работы сайта.<br />
			Удалите или переименуйте <code>install<?php echo EXT ?></code> после ввода настроек MySQL</p>
	<?php endif ?>

	
        <h1>Настройки MySQL</h1>
        <p>Перед началом работы с сайтом обязательно введите и сохраните настройки MySQL</p>
        <form action="" method="post">
        <table>            
            <tr>
                <td>Сервер (host)</td>
                <td><input type="text" value="localhost" name="hostname" /></td>
            </tr>
            <tr>
                <td>Пользователь (username)</td>
                <td><input type="text" value="root" name="username" /></td>
            </tr>
            <tr>
                <td>Пароль (password)</td>
                <td><input type="password" value="" name="password" /></td>
            </tr>
            <tr>
                <td>База данных (database)</td>
                <td><input type="text" value="" name="database" /></td>
            </tr>
            <tr>

                <?php if (is_writable('modules/database/config/database.php') OR @chmod('modules/database/config/database.php',0777)): ?>
                    <?php if (count($_POST)): ?>
                    <td colspan="2"><b>Сохранено!</b> Удалите или переименуйте install<?php echo EXT ?> и приступайте к настройке магазина </td>
                    <?php else: ?>
                    
                    <td colspan="2">
                        <input type="checkbox" name="importSQL" checked="1" />Создать в базе структуру таблиц
                        <input type="submit" value="Сохранить"/>
                        <?php if (!is_writable('install.php')): ?>
                                <br>После нажатия на кнопку Сохранить переименуйте файл install.php
                        <?php endif ?>
                    </td>
                    <?php endif ?>
                <?php else: ?>
                    <td class="fail" colspan="2">Файл /modules/database/config/database.php не доступен для записи. </td>
                <?php endif ?>
            </tr>
        </table>
        </form>

<p style="float:right;"><a href="http://php5shop.com" title="php5shop">php5shop © 2011-2016</a></p>
</body>
</html>
