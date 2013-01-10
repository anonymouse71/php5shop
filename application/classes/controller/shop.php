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
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

class Controller_Shop extends Controller_Template
{

    public $theme = 'default'; //тема
    public $template = 'themes/default/index'; //свойство класса Controller_Template
    public static $cache; //объект модуля кэширования

    private $currency = DEFAULT_CURRENCY; //валюта
    private $blogLimit = 5; //записей на 1 странице блога
    private $productsOnPage = 10; //продуктов на странице
    private $categoriesObject; //массив с категориями (сохранен после pre() для уменьшения количества запросов к БД)
    private $user; //объект с данными о пользователе
    private $apis; //массив данных для интеграции с сторонними сервисами

    /**
     * Получение настроек, подготовка блоков страницы, инициализация
     */
    public function before()
    {
        parent::before(); //метод выполняется перед каждым action

        // Список id кэшируемых объектов: menuItem, htmlBlocks, apis, cats, LastProd
        // их кэш нужно удалять при модификации данных в БД: Cache::instance()->delete($id);         
        self::$cache = Cache::instance();

        define('TPL', 'themes/' . $this->theme . '/');
        $tpl =& $this->template; //для сокращения

        //получение списка кнопок меню, которые должны быть показаны по настройкам     
        $tpl->menu = self::$cache->get('menuItem');
        if (false === $tpl->__isset('menu'))
        {
            $tpl->menu = Model::factory('menuItem')->get();
            self::$cache->set('menuItem', $tpl->menu);
        }

        //получение содержимого пользовательских HTML блоков
        $htmlBlocks = self::$cache->get('htmlBlocks');
        if (null === $htmlBlocks)
        {
            $htmlBlocks = Model::factory('html')->getblocks();
            self::$cache->set('htmlBlocks', $htmlBlocks);
        }

        //подключение CSS
        $tpl->css = new View(TPL . 'css');

        $this->apis = self::$cache->get('apis');
        if (null === $this->apis)
        {
            $this->apis = Model_Apis::get();
            self::$cache->set('apis', $this->apis);
        }

        $googleAnalytics = new View('analytics'); //подключение представления для кода google analistics
        $googleAnalytics->set('account', $this->apis['analytics']); //подстановка кода аккаунта google analistics

        $tpl->css .= $googleAnalytics; //добавление кода между <head> и </head>
        $tpl->topBlock3 = $htmlBlocks['headerWidg']; //подстановка переменных в шаблон
        $tpl->keywords = htmlspecialchars($htmlBlocks['keywords']); //переменная с ключевыми словами для SEO оптимизации
        $tpl->banner1 = $htmlBlocks['banner1']; //HTML код 4 баннеров или пользовательских блоков
        $tpl->banner2 = $htmlBlocks['banner2'];
        $tpl->banner3 = $htmlBlocks['banner3'];
        $tpl->banner4 = $htmlBlocks['banner4']
            . Model_Sape_client::links($this->apis['sape']); //подключение сервиса Sape.ru
        $tpl->logo = $htmlBlocks['logo']; //логотип
        $tpl->about = $htmlBlocks['about']; //HTML код с описанием магазина
        $tpl->title = htmlspecialchars($htmlBlocks['shopName']); //название магазина
        $tpl->lastNews = ''; //блок с последней записью из блога
        $tpl->topBlock2 = new View(TPL . 'topCart'); //корзина
        $tpl->topBlock2->items = 0; //количество товаров в корзине
        $tpl->topTitle = $htmlBlocks['topTitle']; //заголовок магазина вверху в виде html

        $tpl->prod1 = Session::instance()->get('prod1'); //получение номеров товаров для сравнения
        $tpl->prod2 = Session::instance()->get('prod2');
        if (!isset($tpl->prod1)) //в сессии нет id товара
        {
            $tpl->prod1 = '';
        }
        else //товар найден, получаем его название
        {
            $p = ORM::factory('product', $tpl->prod1)->as_array();
            $tpl->prod1 = ($p['name']) ? $p['name'] : ''; //название найдено? подставляем в шаблон
        }
        if (!isset($tpl->prod2)) //в сессии нет id товара
        {
            $tpl->prod2 = '';
        }
        else //товар найден, получаем его название
        {
            $p = ORM::factory('product', $tpl->prod2)->as_array();
            $tpl->prod2 = ($p['name']) ? $p['name'] : ''; //название найдено? подставляем в шаблон
        }


        if (is_array(Session::instance()->get('cart')))
        {
            $cart = array_unique(Session::instance()->get('cart')); //удаление повторов
            $tpl->topBlock2->items = count($cart); //подстановка количества товаров в корзине
            Session::instance()->set('cart', $cart); //запись без дубликатов
        }

        $this->boolConfigs = Model::factory('config')->getbool(); //получение пользовательских настроек

        if (!$this->boolConfigs['ShowBlog']) //если функция блога отключена
        {
            $tpl->menu[2] = FALSE;
        } //прячем кнопку "Новости" из меню
        else //иначе
        {
            if ($this->boolConfigs['LastNews']) //если включен блок последних новостей, вставляем в него последнюю запись из блога (200 симв.)
            {
                $tpl->lastNews = Model::factory('BlogPost')->last(200);
            }
        }


        if (strlen(Session::instance()->get('currency')) === 3
        ) //если в сессии есть 3-буквенный банковский код, устанавливаем его как валюту
        {
            $this->currency = Session::instance()->get('currency');
        }
        $currency = Model::factory('config')->getCurrency(); //получение валют из БД
        if ($this->boolConfigs['currency']) //согласно настройкам
        {
            $tpl->topBlock1 = new View(TPL . 'currency'); //подключение шаблона блока выбора валюты
            $tpl->topBlock1->currency = $this->currency; //подстановка выбранной валюты
            $tpl->topBlock1->array = array_keys(
                $currency
            ); //подстановка в шаблон переменной с массивом названий (банковских кодов) валют
        }

        $this->auth = Auth::instance(); //инициализация механизма авторизации


        $tpl->loginForm = new View(TPL . 'loginForm'); //подключаем шаблон формы авторизации.
        if (Session::instance()->get('login_error') == 1
        ) //если пользователь уже пробовал авторизоваться и допустил ошибку, о которой свидетельствует COOKIES
        {
            $tpl->loginForm .= new View('modalLoginError'); //добавляем всплывающее окно
            Session::instance()->delete(
                'login_error'
            ); //удаляем переменную из COOKIES чтобы сообщеие больше не повторялось (до следующей ошибки авторизации)
        }

        if (Session::instance()->get('needEmail'))
        {
            $tpl->loginForm .= new View('needEmailError'); //добавляем всплывающее окно
            Session::instance()->delete('needEmail'); //удаляем переменную из COOKIES
        }

        if (Session::instance()->get('okEmail'))
        {
            $vOkEmail = new View('okEmail');
            $vOkEmail->ok = (Session::instance()->get('okEmail') == 1);
            $tpl->loginForm .= $vOkEmail;
            Session::instance()->delete('okEmail');
        }

        if (Session::instance()->get('password_changed') == 1
        ) //если пользователь использовал систему восстановления пароля
        {
            $tpl->loginForm .= new View('passwordsend'); //добавляем всплывающее окно
            Session::instance()->delete(
                'password_changed'
            ); //удаляем переменную из COOKIES чтобы сообщеие больше не повторялось
        }

        if (!$this->auth->logged_in()) //если пользователь не авторизован
        {
            $this->auth->auto_login();
        } //инициализации автовхода по COOKIES.
        if (!$this->auth->logged_in()) //если пользователь все равно не авторизован
        {
            $tpl->menu[6] = FALSE; //прячем кнопку "Аккаунт" из меню
        }
        else //авторизован?
        { //получаем данные о пользователе и вместо формы авторизации показываем имя и кнопку выхода
            $tpl->loginForm = new View(TPL . 'exitForm');
            $this->user = $this->auth->get_user();
            $tpl->loginForm->user = $this->user->username;
        }


        if (!$this->auth->logged_in('admin')) //если пользователь не авторизован как администратор,
        {
            $tpl->menu[5] = FALSE;
        } //убераем из меню кнопку панели управления

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
        {
            $tpl->cats = $this->categoriesObject->menu2( //подстановка дерева категорий в представление
                url::base() . 'shop/category',
                $this->request->param('catid')
            );
        }
        else
        {
            $tpl->cats = $this->categoriesObject->menu(
                url::base() . 'shop/category',
                $this->request->param('catid')
            );
        }

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
            {
                $pollV->cookie = Model_Vote::is_voted($this->user->id);
            }
            $tpl->loginForm .= $pollV;
        }

    }

    public function action_index() //главная страница
    {
        $pct = 1; //множитель скидки для незарегистрированных пользователей
        $sessionCart = Session::instance()->get('cart'); //получаем содержимое корзины
        if (!is_array($sessionCart)) //в массив $sessionCart
        {
            $sessionCart = array();
        }
        $bcart = Session::instance()->get('bigCart'); //массив количества товаров
        $curr = Model::factory('config')->getCurrency($this->currency); //получение курса валют
        if (!$curr) //если в сессию попал поддельный или уже не существующих в БД курс
        {
            Session::instance()->delete('currency'); //удаляем его из сессии
            $this->request->redirect($_SERVER['REQUEST_URI']); //и обновляем страницу
        }
        $this->template->stuff = new View(TPL . 'products'); //Подключаем представление

        $product = $this->request->param('product'); //получение GET переменных
        $cat = $this->request->param('catid');
        $page = isset($_GET['page']) ? abs((int)$_GET['page']) : 0; //номер страницы >=0
        if (!$page) // > 0
        {
            $page++;
        }
        if (isset($this->user->id)) //если пользователь зарегистрирован
        {
            $pct = Model::factory('group')->get_pct($this->user->id); //получаем множитель скидки для пользователя
        }

        if ($product) //указан продукт
        {
            $product = ORM::factory('product', $product)->as_array(); //находим продукт в БД
            if (!$product['id']) //если его там нет,
            {
                $this->request->redirect(url::base());
            } //перенаправляем на главную
            else //если есть,
            { //учитываем скидку, курс валют и добавляем к цене банковский код валюты:
                $product['price'] = round($curr * $product['price'] * $pct, 2);
                $product['price'] .= ' ' . $this->currency;
                $product['name'] = htmlspecialchars($product['name']);
                $product['cart'] = FALSE; //по умолчанию продукт не в корзине
                if (is_array($sessionCart)) //но если корзина записана как массив
                {
                    if (in_array($product['id'], $sessionCart)) //и продукт в ней записан
                    {
                        $product['cart'] = TRUE;
                    }
                } //передадим эту информацию в шаблон
                $this->template->stuff = new View(TPL . 'oneProduct'); //подключаем шаблон для 1 продукта
                $this->template->stuff->comments = $this->boolConfigs['comments'];
                if ($this->boolConfigs['bigCart'])
                {
                    if (isset($bcart[$product['id']]))
                    {
                        $product['bigcart'] = $bcart[$product['id']];
                    }
                    else
                    {
                        if (in_array($product['id'], $sessionCart))
                        {
                            $product['bigcart'] = 1;
                        }
                    }
                }


                $this->template->stuff->item = $product; //вставляем в шаблон данные о продукте
                $description = new View(TPL . 'description');
                $description->text = ORM::factory('description', $product['id'])->__get('text');
                if ($this->auth->logged_in('admin'))
                {
                    $description->id = $product['id'];
                }

                $this->template->title .= ' - ' . $product['name'];
                $this->template->about = ''; //блок приветствия не отображаем
            }
        }
        elseif ($cat) //указана категория
        {
            Session::instance()->set('cat', $cat);
            $categories = $this->categoriesObject->getCatChilds($cat);

            if (!count($categories) || !isset($this->categoriesObject->categories['names'][$cat]))
            {
                $this->request->redirect(url::base() . 'error/404');
            }

            $products = Model::factory('product')->byCategory($categories, $page, $this->productsOnPage);
            $productsCount = ORM::factory('product')->where('cat', 'in', $categories)->count_all();
            if (!$productsCount) //нет товаров в категории
            {
                $this->template->stuff = 'В этой категории сейчас нет товаров.';
            } //вставляем сообщение
            else
            {
                $sortSelect = new View(TPL . 'sort');
                $sortSelect->__set('type', Model_Product::getSort());
                $this->template->topBlock2 .= $sortSelect;
                foreach ($products as $k => $p)
                { //учитываем скидку, курс валют и добавляем к цене банковский код валюты:
                    $products[$k]['price'] = round($curr * $p['price'] * $pct, 2) . ' ' . $this->currency;
                    $products[$k]['cart'] = FALSE; //добавляем информацию
                    if (is_array($sessionCart)) //о наличии или отсутствии продукта в корзине
                    {
                        if (in_array($products[$k]['id'], $sessionCart))
                        {
                            $products[$k]['cart'] = TRUE;
                        }
                    }
                    if ($this->boolConfigs['bigCart']) //если в настройках пользователю разрешено выбирать количество товаров
                    {
                        if (isset($bcart[$p['id']])) //если товара выбрано больше 1 ед.
                        {
                            $products[$k]['bigcart'] = $bcart[$p['id']];
                        } //вставляем колич. ед. в массив
                        else //иначе если
                        {
                            if (in_array($p['id'], $sessionCart)) //товар выбран в количестве 1 ед.
                            {
                                $products[$k]['bigcart'] = 1;
                            }
                        } //записываем колич. 1
                    }

                }
                $this->template->stuff->products = $products; //заполняем его продуктами
                $Pagination = new Pagination(array( //создаем навигацию
                                                    'uri_segment'    => 'page',
                                                    'total_items'    => $productsCount,
                                                    'items_per_page' => $this->productsOnPage,
                                             ));
            }

            $this->template->title .= ' - ' . //добавляем в заголовок страницы
                $this->categoriesObject->categories['names'][$cat] . //название категории
                ' - Страница ' . $page . ''; //и страницу

            $this->template->about = Model_Descr_cat::get($cat); //описание категории

        }
        else //категория не указана
        {
            $products = self::$cache->get('LastProd');
            if (null === $products)
            {
                $products = Model::factory('product')->getLast($this->productsOnPage);
                self::$cache->set('LastProd', $products);
            }

            foreach ($products as $k => $p)
            { //учитываем курс валют, скидку и добавляем к цене банковский код валюты:
                $products[$k]['price'] = round($curr * $p['price'] * $pct, 2) . ' ' . $this->currency;
                $products[$k]['cart'] = FALSE; //добавляем информацию
                $products[$k]['name'] = htmlspecialchars($products[$k]['name']);
                if (in_array($products[$k]['id'], $sessionCart)) //о наличии или отсутствии продукта в корзине
                {
                    $products[$k]['cart'] = TRUE;
                }

                if ($this->boolConfigs['bigCart']) //если в настройках пользователю разрешено выбирать количество товаров
                {
                    if (isset($bcart[$p['id']])) //если товара выбрано больше 1 ед.
                    {
                        $products[$k]['bigcart'] = $bcart[$p['id']];
                    } //вставляем колич. ед. в массив
                    else //иначе если
                    {
                        if (in_array($p['id'], $sessionCart)) //товар выбран в количестве 1 ед.
                        {
                            $products[$k]['bigcart'] = 1;
                        }
                    } //записываем колич. 1
                }
            }

            $this->template->stuff->products = $products; //заполняем представление продуктами
        }
        if ($this->boolConfigs['bigCart'] && is_object($this->template->stuff)) //указываем опцию из настроек
        {
            $this->template->stuff->bigcart = 1;
        }
        if (isset($description))
        {
            if (strlen($this->apis['vkcomments']))
            {
                $this->template->css .= View::factory('vk')->set('apiId', $this->apis['vkcomments']);
                $description->__set('vk_on', TRUE);
            }
            else
            {
                $description->__set('vk_on', FALSE);
            }
            if (strlen($this->apis['disqus']))
            {
                $description->__set('disqus_shortname', $this->apis['disqus']);
            }
            $rating = new View('rating');
            $rating->val = 0;
            $r = ORM::factory('rating_value', $product['id']);
            $rating->val = $r->__get('val');
            $rating->disable = !(bool)$this->user;

            //если страница 1 товара и можно комментировать
            if (isset($this->template->stuff->comments) && $this->template->stuff->comments && isset($product['id']))
            {
                $description .= Model_Comment::form($product['id'], TRUE);
            }
            $this->template->stuff .= $rating . $description;
        }

        if (isset($Pagination))
        {
            $this->template->stuff .= $Pagination;
        }
    }

    public function action_register()
    {
        $this->template->title .= ' - Регистрация'; //дополнение заголовка страницы
        if ($this->auth->get_user()) //если пользователь уже авторизован,
        {
            exit($this->request->redirect(url::base()));
        } //перенаправим на главную страницу.

        $captcha = Captcha::instance(); //инициализируем механизм проверочного изображения
        $this->template->about = new View(TPL . 'registerForm'); //подключение формы
        $this->template->about->captcha = $captcha; //подстановка в форму captcha
        $this->template->about->fields = ORM::factory('field')->find_all()->as_array();
        if ($this->boolConfigs['invite']) //если включена система инвайтов
        {
            $fieldInvite = ORM::factory('field'); //создаем дополнительное регистрационное поле
            $fieldInvite->__set('id', 'invite'); //(в HTML) тэг name = finvite
            $fieldInvite->__set('name', 'Код приглашения, если есть'); //имя
            $fieldInvite->__set('type', 2); //тип "числа, буквы, пробелы"
            $fieldInvite->__set('empty', 1); //может быть пустым

            $this->template->about->fields += array(-1 => $fieldInvite); //добавляем поле в представление
        }
        $this->template->about->errors = ''; //объявление переменной для ошибок
        $errors = Session::instance()->get('register_errors'); //получение информации об ошибках из сессии
        $errStr = '';
        if (is_array($errors)) //если есть ошибки
        { //записываем в переменную
            $errStr = 'При заполнении были допущены ошибки! ';
            foreach ($errors as $key => $value)
            {
                $errStr .= $key . $value . '. ';
            } //вместе с ключами массива
        }

        $this->template->about->val = Session::instance()->get(
            'register_post'
        ); //подключение в шаблон данных, которые были введены в прошлый раз (если тогда были ошибки)

        Session::instance()->delete('register_errors'); //удаление полученых данных из сессии
        Session::instance()->delete('register_post');
        //замена ключей массива на русский текст, чтобы не пугать пользоватей, абсолютно не знающих английского
        if (strlen($errStr))
        {
            $errStr = str_replace('username', 'Имя', $errStr);
            $errStr = str_replace('password', 'Пароль', $errStr);
            $errStr = str_replace('Пароль_confirm', 'Пароль повторно', $errStr);
            $errStr = str_replace('phone', 'Телефон', $errStr);
        }
        $this->template->about->errors = $errStr;

    }

    public function action_user()
    {
        if (!$this->user) //если пользователь не авторизован,
        {
            exit($this->request->redirect(url::base()));
        } //перенаправим на главную страницу.
        $this->template->about = new View(TPL . 'userPage');
        $pct = Model::factory('group')->get_pct($this->user->id); //получаем множитель скидки
        if ($pct != 1) //если он не равен 1
        {
            $this->template->about->pct = (100 * (1 - $pct)) . '%';
        } //подставляем в представление
        if (Auth::instance()->logged_in('admin'))
        {
            $this->template->about->adm = 1;
        }

        if (ORM::factory('field')->count_all())
        {
            $this->template->about->fields = ORM::factory('field')->find_all();
            $fieldORM = ORM::factory('field_value');
            foreach ($this->template->about->fields as $field)
            {
                $fieldVals[$field->id] = $fieldORM->get($field->id, $this->user->id);
            }
            $this->template->about->fieldVals = $fieldVals;
        }
        if ($this->boolConfigs['refpp'])
        {
            $this->template->about->balance = Model::factory('balance_user')->balance($this->user->id);
            $this->template->about->info = str_replace(
                '{{link}}', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . url::base()
                . $this->user->id, Model::factory('affiliate')->about()
            );
        }
        $this->template->about->user = $this->user; //подставляем данные пользователя
        $this->template->title .= ' - Личная страница ' . $this->user->username; //дополнение заголовка страницы
    }

    public function action_clients()
    {
        if ($this->template->menu[4]) //если страница включена
        {
            $this->template->about = Model::factory('html')->getHtml(1); //подстановка HTML
            $this->template->title .= ' - Наши клиенты'; //дополнение заголовка страницы
        }
        else
        {
            $this->request->redirect(url::base());
        }
    }

    public function action_contacts()
    {
        if ($this->template->menu[3]) //если страница включена
        {
            $this->template->about = Model::factory('html')->getHtml(2); //подстановка HTML
            $this->template->title .= ' - Наши контакты'; //дополнение заголовка страницы
        }
        else
        {
            $this->request->redirect(url::base());
        }
    }

    public function action_cart()
    {
        $this->template->title .= ' - Покупки';
        $cart = Session::instance()->get('cart');
        if (!is_array($cart))
        {
            $cart = array();
        }
        $cart = array_unique($cart);
        $bcart = Session::instance()->get('bigCart');
        if (!is_array($bcart))
        {
            $bcart = array();
        }
        $this->template->about = new View(TPL . 'shopingCart');
        $i = 0;
        $curr = Model::factory('config')->getCurrency($this->currency); //получение курса валют
        if (!$curr) //если в сессию попал поддельный или уже не существующих в БД курс
        {
            Session::instance()->delete('currency'); //удаляем его из сессии
            $this->request->redirect($_SERVER['REQUEST_URI']); //и обновляем страницу
        } //получаем множитель скидки для пользователя
        $pct = isset($this->user->id) ? Model::factory('group')->get_pct($this->user->id) : 1;
        foreach ($cart as $id)
        {
            $products[$i] = ORM::factory('product', $id)->as_array();
            if (is_array($products[$i]))
            {
                $products[$i]['price'] = round($curr * $products[$i]['price'] * $pct, 2) . ' ' . $this->currency;
                if (isset($bcart[$id]))
                {
                    $products[$i]['count'] = $bcart[$id];
                }
                else
                {
                    $products[$i]['count'] = 1;
                }
                $i++;
            }
        }
        if (isset($products) && is_array($products))
        {
            $this->template->about->products = $products;
        }
        else
        {
            $this->template->about->products = array();
        }

        $this->template->about->sum = Model_Ordproduct::sum();
    }

    public function action_blog($id = 0)
    {
        $is_admin = Auth::instance()->logged_in('admin');
        if ($id) //если он установлен
        {
            $view = new View(TPL . 'blogPost'); //подключение отображения
            $view->post = Model::factory('BlogPost', $id); //получение записи
            if (!isset($view->post->id)) //нет записи с таким id?
            {
                exit($this->request->redirect(url::base() . 'error/404'));
            } //перенаправление на страницу 404
            $view->post->title = htmlspecialchars($view->post->title);
            $view->is_admin = $is_admin;
            $this->template->about = $view; //запись есть? вставляем в страницу
            $this->template->title .= ' - ' . $view->post->title; //дополняем заголовок страницы
            //добавляем комментарии
            if ($this->boolConfigs['comments'])
            {
                $this->template->about .= Model_Comment::form($id, FALSE);
            }

        }
        else //id не установлен
        {
            $page = isset($_GET['page']) ? abs((int)$_GET['page'])
                : 0; //получение GET параметра page с установкой его >= 0
            if (!$page) //если он равен 0
            {
                $page = 1;
            } //устанавливаем в 1
            $array = Model::factory('BlogPost')->read($page, $this->blogLimit); //считываем несколько последних записей
            $this->template->about = '';
            foreach ($array as $post) //каждую оборачиваем в представление
            { //и добавляем в $this->template->about
                $view = new View(TPL . 'blogPost');
                $view->post = $post;
                if ($view->post->html2) //строка на случай если ф-я комментирования добавлена в версию движка "на гарячую"
                {
                    $view->post->html = $view->post->html2;
                } //показываем сокращенныую новость вместо полной

                $view->post->title = htmlspecialchars($view->post->title); //не будет html кода в заголовке
                $view->is_admin = $is_admin;
                $this->template->about .= $view;
            }

            $this->template->about .= new Pagination(array(
                                                          'uri_segment'    => 'page',
                                                          'total_items'    => Model::factory('BlogPost')->find_all()
                                                              ->count(),
                                                          'items_per_page' => $this->blogLimit,
                                                     ));
            $this->template->title .= ' - Новости магазина'; //дополняем заголовок страницы

        }
    }

    public function action_forgotpassword() //забыли пароль
    {
        if ($this->auth->get_user()) //если пользователь уже авторизован,
        {
            exit($this->request->redirect(url::base()));
        } //перенаправим на главную страницу.

        $captcha = Captcha::instance(); //инициализируем механизм проверочного изображения
        $this->template->about = new View(TPL . 'forgotPassword'); //подключение формы
        $this->template->about->captcha = $captcha; //подстановка в форму captcha
        //получение информации об ошибках из сессии
        $this->template->about->errors = Session::instance()->get('emailpass_errors');
        Session::instance()->delete('emailpass_errors'); //удаление переменной SESSION
    }

    public function action_currency($code = 0) //Смена валюты
    { //в настройках машрутизации регулярное выражение [A-Z]{3}
        if ($code) //если параметр <code> установлен
        {
            Session::instance()->set('currency', $code);
        } //записываем в сессию

        if (!Request::$referrer)
        {
            Request::$referrer = url::base();
        }
        if (FALSE === strpos(Request::$referrer, '://' . $_SERVER['HTTP_HOST'])
        ) //если HTTP_REFERER содержит домен сайта
        {
            Request::$referrer = url::base();
        } //на главную страницу
        $this->request->redirect(Request::$referrer);
    }

    public function action_sortset($code = 0)
    {
        Session::instance()->set('sort', $code);

        $this->request->redirect(
            url::base() . 'shop/category'
                . Session::instance()->get('cat', 1)
        );
    }

    public function action_order($phone = null)
    {
        if (null != $phone)
        {
            $phone = preg_replace('|[\s\-+]|', '', $phone);
        }
        $way = null; //способ заказа
        if (isset($_POST['way']) && ORM::factory('pay_type', $_POST['way']))
        {
            Session::instance()->set('way', $_POST['way']);
        }
        if (isset($_POST['unsetway']))
        {
            Session::instance()->delete('way');
        }

        $this->template->about = new View('order');
        $this->template->about->message = FALSE;
        $emails = ORM::factory('mail')->find_all()->as_array('id', 'value'); //получение email и jabber менеджера
        $to = array();
        if ($this->boolConfigs['ordMail'] && isset($emails[1])
        ) //найден email и в настройках установлено отправлять на него
        {
            $to['email'] = $emails[1];
        }
        if ($this->boolConfigs['ordJabb'] && isset($emails[2])
        ) //найден jabber и в настройках установлено отправлять на него
        {
            $to['jabber'] = $emails[2];
        }
        $products = Session::instance()->get('cart'); //получение списка продуктов из корзины
        $counts = Session::instance()->get('bigCart');
        if (!is_array($products) || !count($products)) //если продуктов там нет
        {
            $this->request->redirect(url::base());
        } //перенаправление
        $stop = $this->boolConfigs['regOrder']; //Нельзя совершать покупки без регистрации?
        $this->template->about->stop = $stop;
        $way = Session::instance()->get('way');
        if (ORM::factory('pay_type')->count_all() == 1)
        {
            $way = ORM::factory('pay_type')->find()->__get('id');
        }
        else
        {
            $this->template->about->ways = ORM::factory('pay_type')->where('active', '=', 1)->find_all();
        }

        if (Auth::instance()->logged_in()) //пользователь авторизован?
        {
            $user = Auth::instance()->get_user()->as_array(); //извлекаем данные о нем в массив
            if (isset($user['phone']) && $way && isset($_POST['confirm'])) //есть все необходимое для заказа
            {
                $message = Model_Order::create($products, $user, $to, $way, $counts);
            }
            //заказ сохранен

            $this->template->about->register = 1;
        }
        elseif ($phone && strlen($phone) >= 6 && strlen($phone) <= 12 && !$stop && $way && isset($_POST['confirm']))
        {
            $message = Model_Order::create(
                $products, array('phone' => $phone), $to, $way, $counts
            );
        } //заказ сохранен
        elseif (!$phone)
        {
            $this->template->about->nophone = 1;
        }

        if ($way)
        {
            $this->template->about->way = ORM::factory('pay_type', $way);
            $text = $this->template->about->way->text;
            $text = str_replace('{{id}}', Model::factory('Tmp_Order')->new_id(), $text); //предварительный id заказа
            $text = str_replace('{{sum}}', Model_Ordproduct::sum(), $text); //сумма заказа


            if (isset($this->user->id))
            {
                $balance = Model::factory('balance_user')->balance($this->user->id);
                $text = str_replace('{{refpp}}', $balance, $text);
            }
            elseif (FALSE !== strpos($text, '{{refpp}}'))
            {
                $this->template->about->stop = 1;
                $text = '';
                $way = 0;
                Session::instance()->delete('way');
            }

            $this->template->about->way->text = $text;
        }

        if (isset($message)) //статус = ok
        {
            $this->template->about->message = $message;
            Session::instance()->delete('cart'); //корзина очищена
            Session::instance()->delete('bigCart');
            Session::instance()->delete('way');
            $this->template->topBlock2->items = 0; //виджет корзины обновлен
        }
    }

    public function action_referral($id)
    {
        if ($this->boolConfigs['refpp'])
        {
            Session::instance()->set('referral', $id);
        }
        $this->action_index();
    }

    public function action_compare()
    {
        $pct = 1; //множитель скидки для незарегистрированных пользователей
        $sessionCart = Session::instance()->get('cart'); //получаем содержимое корзины
        if (!is_array($sessionCart)) //в массив $sessionCart
        {
            $sessionCart = array();
        }
        $bcart = Session::instance()->get('bigCart'); //массив количества товаров
        $curr = Model::factory('config')->getCurrency($this->currency); //получение курса валют
        if (!$curr) //если в сессию попал поддельный или уже не существующих в БД курс
        {
            Session::instance()->delete('currency'); //удаляем его из сессии
            $this->request->redirect($_SERVER['REQUEST_URI']); //и обновляем страницу
        }
        $this->template->stuff = new View(TPL . 'products'); //Подключаем представление

        $product1 = Session::instance()->get('prod1'); //получение номеров товаров
        $product2 = Session::instance()->get('prod2');

        if (isset($this->user->id)) //если пользователь зарегистрирован
        {
            $pct = Model::factory('group')->get_pct($this->user->id); //получаем множитель скидки для пользователя
        }

        if ($product1 && $product1) //указаны продукты
        {
            $product1 = ORM::factory('product', $product1)->as_array(); //находим продукт в БД
            $product2 = ORM::factory('product', $product2)->as_array();
            if (!$product1['id'] || !$product2['id']) //если там нет,
            {
                $this->request->redirect(url::base());
            } //перенаправляем на главную
            else //если есть,
            { //учитываем скидку, курс валют и добавляем к цене банковский код валюты:
                $product1['price'] = round($curr * $product1['price'] * $pct, 2);
                $product1['price'] .= ' ' . $this->currency;
                $product1['name'] = htmlspecialchars($product1['name']);
                $product1['cart'] = FALSE; //по умолчанию продукт не в корзине
                if (is_array($sessionCart)) //но если корзина записана как массив
                {
                    if (in_array($product1['id'], $sessionCart)) //и продукт в ней записан
                    {
                        $product1['cart'] = TRUE;
                    }
                } //передадим эту информацию в шаблон
                $product2['price'] = round($curr * $product2['price'] * $pct, 2);
                $product2['price'] .= ' ' . $this->currency;
                $product2['name'] = htmlspecialchars($product2['name']);
                $product2['cart'] = FALSE; //по умолчанию продукт не в корзине
                if (is_array($sessionCart)) //но если корзина записана как массив
                {
                    if (in_array($product2['id'], $sessionCart)) //и продукт в ней записан
                    {
                        $product2['cart'] = TRUE;
                    }
                } //передадим эту информацию в шаблон
                $this->template->stuff = new View(TPL . 'twoProducts'); //подключаем шаблон

                if ($this->boolConfigs['bigCart'])
                {
                    if (isset($bcart[$product1['id']]))
                    {
                        $product1['bigcart'] = $bcart[$product1['id']];
                    }
                    else
                    {
                        if (in_array($product1['id'], $sessionCart))
                        {
                            $product1['bigcart'] = 1;
                        }
                    }
                    if (isset($bcart[$product2['id']]))
                    {
                        $product2['bigcart'] = $bcart[$product2['id']];
                    }
                    else
                    {
                        if (in_array($product2['id'], $sessionCart))
                        {
                            $product2['bigcart'] = 1;
                        }
                    }
                }


                $this->template->stuff->item1 = $product1; //вставляем в шаблон данные о продукте
                $this->template->stuff->item2 = $product2;
                $description1 = new View(TPL . 'description');
                $description1->__set('vk_on', FALSE);
                $description1->text = ORM::factory('description', $product1['id'])->__get('text');
                if ($this->auth->logged_in('admin'))
                {
                    $description1->id = $product1['id'];
                }
                $description2 = new View(TPL . 'description');
                $description2->__set('vk_on', FALSE);
                $description2->text = ORM::factory('description', $product2['id'])->__get('text');
                if ($this->auth->logged_in('admin'))
                {
                    $description2->id = $product2['id'];
                }

                $this->template->stuff->description1 = $description1;
                $this->template->stuff->description2 = $description2;
                $this->template->title .= ' - Сравнение ' . $product1['name'] . ' и ' . $product2['name'];
                $this->template->about = ''; //блок приветствия не отображаем
                $this->template->prod1 = ''; //блок сравнения не отображаем

                //Session::instance()->delete('prod1');                           //сравнение завершено
                //Session::instance()->delete('prod2');

            }
        }
        else //товары не указаны
        {
            $this->template->title .= ' - Страница сравнения товаров ';
            $this->template->about = '<h2>Не выбраны товары для сравнения</h2>';
            $this->template->stuff->products = array();
        }

        if ($this->boolConfigs['bigCart'] && is_object($this->template->stuff)) //указываем опцию из настроек
        {
            $this->template->stuff->bigcart = 1;
        }
    }

    public function after()
    {
        parent::after();
        //если в настройках это включено, в footer добавляется Benchmark
        if ($this->boolConfigs['timeFooter'] && isset($this->template->banner4))
        {
            $this->template->banner4 .= Model::factory('Benchmark')->getTime();
        }
    }
} // End Controller_Shop
