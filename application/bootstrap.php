<?php defined('SYSPATH') or die('No direct script access.');
/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2010-2012 phpdreamer
 * php5shop.com
 * email: phpdreamer@rambler.ru
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3.
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/.
 */

/**
 * Не останавливать выполнение сколько позволяет настройка сервера
 */
@set_time_limit(ini_get('max_execution_time'));
/**
 * Сжимать код перед отправкой (GZIP сжатие)
 */
@ob_start('ob_gzhandler', 9);
/**
 * Временная зона ('Europe/London','Europe/Berlin','Europe/Kiev','Europe/Moscow',
 * 'Europe/Samara','Asia/Yekaterinburg','Asia/Novosibirsk','Asia/Krasnoyarsk','Asia/Irkutsk'...)
 */
if (function_exists('date_default_timezone_set'))
{
    date_default_timezone_set('Europe/Kiev');
}
/**
 * Валюта по умолчанию (банковский трехбуквенный код)
 */
define('DEFAULT_CURRENCY', 'UAH'); //менять только на валюту, которая есть в базе данных
define('LINE_CURR_CHANGE', __LINE__); //не менять

/**
 * Меню expanded со скрытием подкатегорий
 * TRUE - включено FALSE - обычное меню, показывать все категории стразу
 */
define('MENU2', FALSE);

/**
 * Set the default locale.
 *
 * @see  http://docs.kohanaphp.com/about.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'ru_RU.utf-8');
/**
 * Версия php5shop
 */
define('VERSION', '1.7.4');

spl_autoload_register(array('Kohana', 'auto_load'));
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */

/**
 * Set the production status
 */
define('IN_PRODUCTION', TRUE);

Kohana::init(array('base_url' => '/', 'index_file' => '', 'caching' => TRUE, 'profile' => FALSE));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH . 'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

Cookie::$expiration = 31536000;
/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(
    array(
        'auth' => MODPATH . 'auth',
        'database' => MODPATH . 'database',
        'image' => MODPATH . 'image',
        'orm' => MODPATH . 'orm',
        'pagination' => MODPATH . 'pagination',
        'captcha' => MODPATH . 'captcha',
        'categories' => MODPATH . 'categories',
        'jabber' => MODPATH . 'jabber',
        'xlsreader' => MODPATH . 'xlsreader',
        'PclZip' => MODPATH . 'pclzip',
        'cache' => MODPATH . 'cache',
    )
);

//Маршрут выключает сайт на технический перерыв
//Route::set('off', '<path>',array('path' => '.{0,}'))->defaults(array('controller'=>'error','action'=>'off'));

//маршрут для установки курса валюты
Route::set('curr', 'shop/currency/<code>', array('code' => '[A-Z]{3}'))
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'currency',
            'param' => 'code'
        )
    );

//маршрут для блога
Route::set('blog', 'blog(/<id>)', array('id' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'page',
            'action' => 'blog',
            'param' => 'id'
        )
    );
Route::set('blog_news', 'blog')
    ->defaults(
        array(
            'controller' => 'page',
            'action' => 'blog',
            'param' => ''
        )
    );

//маршрут для добавления продуктов в корзину
Route::set('addtocart', 'ajax/add_to_cart/<id>(/<count>)', array('id' => '[0-9]+', 'count' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'ajax',
            'action' => 'add_to_cart',
        )
    );

//маршрут для голосования
Route::set('poll', 'ajax/vote<id>', array('id' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'ajax',
            'action' => 'vote'
        )
    );

//маршрут для поиска
Route::set('searchproducts', 'ajax/search/<string>', array('string' => '[^/]+'))
    ->defaults(
        array(
            'controller' => 'ajax',
            'action' => 'search',
        )
    );

//маршрут категорий
Route::set('cat', 'category/<catpath>', array('catpath' => '[^/]+'))
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'index'
        )
    );

//старый маршрут категорий для возможности обновления CMS v1.6.8 без потери страниц из индекса
Route::set('cat_old', 'shop/category<catid>', array('catid' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'cat_old'
        )
    );

//маршрут товаров
Route::set('product', 'shop/product<product>', array('product' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'index'
        )
    );

//маршрут для установки статуса заказов
Route::set('changestatus', 'ajax/changestatus/<id>/<status>', array('id' => '[0-9]+', 'status' => '[^/]+'))
    ->defaults(
        array(
            'controller' => 'ajax',
            'action' => 'changestatus',
        )
    );

//маршрут для отображения сообщения о php5shop
Route::set('version', 'about')
    ->defaults(
        array(
            'controller' => 'error',
            'action' => 'version',
        )
    );

//маршрут партнерской программы
Route::set('pp', '<id>', array('id' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'referral',
        )
    );


//стандартный маршрут
Route::set('default', '(<controller>(/<action>(/<id>)))', array('id' => '[0-9]+'))
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'index',
        )
    );

//страница index.php
Route::set('index', '(index.php)')
    ->defaults(
        array(
            'controller' => 'shop',
            'action' => 'indexphp',
        )
    );

//interkassa Status URL
Route::set('interkassa_status', 'interkassa/status/<hash>', array('hash' => '[^/]*'))
    ->defaults(
        array(
            'controller' => 'interkassa',
            'action' => 'status',
            'param' => 'hash'
        )
    );

//маршрут для обработки ошибки 404
Route::set('files', '(<file>)', array('file' => '.+'))
    ->defaults(
    array(
        'controller' => 'page',
        'action' => '404',
    )
);

if (false !== ini_set('session.save_handler', 'files'))
    ini_set('session.save_path', APPPATH . 'sessions');

$request = Request::instance();
try
{
    $request->execute();
}
catch (ReflectionException $e)
{
    $uri = $request->uri;
    $request = Request::factory('page/404');
    $request->uri = $uri;
    $request->execute();
}

if (FALSE !== strpos($request->response, '<html')) //если контент в HTML, минимизируем код
    echo preg_replace('/(\s+)\s{1,}/u', "\n", $request->send_headers()->response);
else
    echo $request->send_headers()->response;
