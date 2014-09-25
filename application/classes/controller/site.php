<?php defined('SYSPATH') or die('No direct script access.');

/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2013-2014 phpdreamer
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
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

class Controller_Site extends Controller_Template
{
    public $theme = 'default2'; //тема по умолчанию
    public static $cache; //объект модуля кэширования
    protected $themes; // массив всех тем, которые есть в каталоге views/themes
    protected $currency = DEFAULT_CURRENCY; //валюта
    protected $blogLimit = 5; //записей на 1 странице блога
    protected $productsOnPage = 10; //продуктов на странице
    protected $categoriesObject; //массив с категориями (сохранен после pre() для уменьшения количества запросов к БД)
    protected $user; //объект с данными о пользователе
    protected $apis; //массив данных для интеграции с сторонними сервисами

    public function __construct(Kohana_Request $request)
    {
        $this->themes = array_slice(scandir(APPPATH . 'views/themes'), 2);
        $session_theme = Cookie::get('theme', $this->theme);
        if (in_array($session_theme, $this->themes))
            $this->theme = $session_theme;
        elseif($this->theme != $session_theme)
            Cookie::delete('theme');
        $this->request = $request;
        // базовый шаблон страницы
        $this->template = 'themes/' . $this->theme . '/index';
    }

    /**
     * Получение настроек, подготовка блоков страницы, инициализация
     */
    public function before()
    {
        parent::before(); //метод выполняется перед каждым action

        // Список id кэшируемых объектов: menuItem, htmlBlocks, apis, cats, LastProd
        // их кэш нужно удалять при модификации данных в БД: Cache::instance()->delete($id);
        self::$cache = Cache::instance();

        if (!defined('TPL'))
            define('TPL', 'themes/' . $this->theme . '/');
        $tpl =& $this->template; //для сокращения

        //получение списка кнопок меню, которые должны быть показаны по настройкам
        $tpl->menu = self::$cache->get('menuItem');
        if (false === $tpl->__isset('menu'))
        {
            $tpl->menu = Model::factory('menuItem')->get();
            self::$cache->set('menuItem', $tpl->menu);
        }

        // дополниетельные страницы ( cached ! )
        $tpl->special_pages = Model_Page::get_menu();

        //получение содержимого пользовательских HTML блоков
        $htmlBlocks = self::$cache->get('htmlBlocks');
        if (null === $htmlBlocks)
        {
            $htmlBlocks = Model::factory('html')->getblocks();
            self::$cache->set('htmlBlocks', $htmlBlocks);
        }

        $this->apis = self::$cache->get('apis');
        if (null === $this->apis)
        {
            $this->apis = Model_Apis::get();
            self::$cache->set('apis', $this->apis);
        }

        $googleAnalytics = new View('analytics'); //подключение представления для кода google analistics
        $googleAnalytics->set('account', $this->apis['analytics']); //подстановка кода аккаунта google analistics

        $tpl->css = $googleAnalytics; //добавление кода между <head> и </head>
        $tpl->topBlock3 = $htmlBlocks['headerWidg']; //подстановка переменных в шаблон

        if (Model_Meta::get_meta('keywords'))
            $tpl->keywords = htmlspecialchars(Model_Meta::get_meta('keywords'));
        else
            $tpl->keywords = htmlspecialchars($htmlBlocks['keywords']);

        if (Model_Meta::get_meta('description'))
            $tpl->description = htmlspecialchars(Model_Meta::get_meta('description'));
        else
            $tpl->description = htmlspecialchars($htmlBlocks['shopName']);
        if (Model_Meta::get_meta('title'))
            $tpl->title = htmlspecialchars(Model_Meta::get_meta('title'));
        else
            $tpl->title = htmlspecialchars($htmlBlocks['shopName']); //название магазина

        $tpl->banner1 = $htmlBlocks['banner1']; //HTML код 4 баннеров или пользовательских блоков
        $tpl->banner2 = $htmlBlocks['banner2'];
        $tpl->banner3 = $htmlBlocks['banner3']
            . Model_Sape_client::links($this->apis['sape']); //подключение сервиса Sape.ru;
        $tpl->banner4 = $htmlBlocks['banner4'];

        $tpl->logo = $htmlBlocks['logo']; //логотип
        $tpl->about = $htmlBlocks['about']; //HTML код с описанием магазина
        $tpl->lastNews = ''; //блок с последней записью из блога
        $tpl->topBlock2 = new View(TPL . 'topCart'); //корзина
        $tpl->topBlock2->items = 0; //количество товаров в корзине
        $tpl->topTitle = $htmlBlocks['topTitle']; //заголовок магазина вверху в виде html

        if (is_array(Session::instance()->get('cart')))
        {
            $cart = array_unique(Session::instance()->get('cart')); //удаление повторов
            $tpl->topBlock2->items = count($cart); //подстановка количества товаров в корзине
            Session::instance()->set('cart', $cart); //запись без дубликатов
        }

        $this->boolConfigs = Model::factory('config')->getbool(); //получение пользовательских настроек

        if (!$this->boolConfigs['ShowBlog']) //если функция блога отключена
            $tpl->menu[2] = FALSE; //прячем кнопку "Новости" из меню
        elseif ($this->boolConfigs['LastNews'])
        {
            //если включен блок последних новостей, вставляем в него последнюю запись из блога (200 симв.)
            $lastNews = Model::factory('BlogPost')->read(1, 13)->as_array();
            if(count($lastNews))
                $tpl->lastNews = View::factory(TPL . 'lastNews', array('data' => $lastNews));
        }

        $tpl->theme = $this->theme;

        if ($this->boolConfigs['theme_ch'])
        {
            // разрешено изменение темы
            if (isset($_POST['theme']))
            {
                // запрос на изменение темы
                Cookie::set('theme', $_POST['theme'], Date::YEAR);
                $this->request->redirect($_SERVER['REQUEST_URI']);
            }
            $tpl->themes = $this->themes;
        }
        else
        {
            if (Cookie::get('theme'))
                Cookie::delete('theme');
            $tpl->themes = array();
        }


        //если в сессии есть 3-буквенный банковский код, устанавливаем его как валюту
        if (strlen(Session::instance()->get('currency')) === 3) //иначе останется валюта по умолчанию
            $this->currency = Session::instance()->get('currency');

        if ($this->boolConfigs['currency']) //согласно настройкам
        {
            $currency = Model::factory('config')->getCurrency(); //получение валют из БД
            //подключение шаблона блока выбора валюты
            $tpl->topBlock1 = new View(TPL . 'currency');
            //подстановка выбранной валюты
            $tpl->topBlock1->currency = $this->currency;
            //подстановка в шаблон переменной с массивом названий (банковских кодов) валют
            $tpl->topBlock1->array = array_keys($currency);
        }

        $this->auth = Auth::instance(); //инициализация механизма авторизации

        $tpl->loginForm = new View(TPL . 'loginForm'); //подключаем шаблон формы авторизации.
        //если пользователь уже пробовал авторизоваться и допустил ошибку, о которой свидетельствует COOKIES
        if (Session::instance()->get('login_error') == 1)
        {
            $tpl->loginForm .= new View('modalLoginError'); //добавляем всплывающее окно
            //удаляем переменную из COOKIES чтобы сообщеие больше не повторялось (до следующей ошибки авторизации)
            Cookie::delete('login_error');
        }

        if (!$this->auth->logged_in()) //если пользователь не авторизован
        { //инициализации автовхода по COOKIES.
            $this->auth->auto_login();
        }

        if (!$this->auth->logged_in()) //если пользователь все равно не авторизован
            $tpl->menu[6] = FALSE; //прячем кнопку "Аккаунт" из меню
        else //авторизован?
        { //получаем данные о пользователе и вместо формы авторизации показываем имя и кнопку выхода
            $tpl->loginForm = new View(TPL . 'exitForm');
            $this->user = $this->auth->get_user();
            $tpl->loginForm->user = $this->user->username;
        }

        if (!$this->auth->logged_in('admin')) //если пользователь не авторизован как администратор,
            $tpl->menu[5] = FALSE;            //убераем из меню кнопку панели управления

        if (!filesize('rss.xml'))
            $tpl->menu[8] = FALSE;

        //получение всех категорий
        $catsArray = self::$cache->get('cats');
        if (null === $catsArray)
        {
            $catsArray = DB::select()->from('categories')->order_by('id')
                ->execute()->as_array();
            self::$cache->set('cats', $catsArray);
        }
        //инициализация класса для построения дерева категорий
        $this->categoriesObject = new Categories($catsArray);

        if (MENU2 === TRUE)
            $tpl->cats = $this->categoriesObject->menu2( //подстановка дерева категорий в представление
                url::base() . 'shop/category',
                $this->request->param('catid')
            );
        else
            $tpl->cats = $this->categoriesObject->menu(
                url::base() . 'shop/category',
                $this->request->param('catid')
            );

        if ($this->boolConfigs['poll'])
        {
            $pollV = new View(TPL . 'poll');
            $pollV->q = Model::factory('poll')->get();
            $pollV->a = ORM::factory('poll_answer')->find_all();
            $countV = 0;
            foreach ($pollV->a as $answer)
            {
                $countV += $answer->count;
            }
            $pollV->count = $countV;
            $pollV->cookie = Session::instance()->get('voted');
            if ($this->user)
                $pollV->cookie = Model_Vote::is_voted($this->user->id);
            $tpl->topBlock3 .= $pollV;
        }

        $tpl->breadcrumbs = array(array('Главная', '/'));
        $tpl->about2 = '';
    }

    public function after()
    {
        parent::after();

        Session::instance()->set('lastPage', $_SERVER['REQUEST_URI']);

        if (!is_object($this->template))
            return;

        //если в настройках это включено, в footer добавляется Benchmark
        if ($this->boolConfigs['timeFooter'] && isset($this->template->banner4))
        {
            $this->template->banner4 .= Model::factory('Benchmark')->getTime();
        }

        $breadcrumbs_a = array();
        foreach ($this->template->breadcrumbs as $breadcrumb)
        {
            $uri = $breadcrumb[1];
            $label = htmlspecialchars($breadcrumb[0]);
            $breadcrumbs_a[] = "<a href='$uri' title='$label'>$label</a>";
        }

        if (count($breadcrumbs_a) > 1)
            $this->template->breadcrumbs = implode(' → ', $breadcrumbs_a);
        else
            $this->template->breadcrumbs = '';
    }
}
