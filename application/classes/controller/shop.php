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

class Controller_Shop extends Controller_Site
{
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
        $page = isset($_GET['page']) ? abs((int)$_GET['page']) : 1; //номер страницы >0
        if (!$page)
            $page = 1;

        if (isset($this->user->id)) //если пользователь зарегистрирован
            $pct = Model::factory('group')->get_pct($this->user->id); //получаем множитель скидки для пользователя

        if ($product) //указан продукт
        {
            $product = ORM::factory('product', $product)->as_array(); //находим продукт в БД
            if (!$product['id']) //если его там нет,
                $this->request->redirect(url::base()); //перенаправляем на главную

            $this->navigation_cat($product['cat']);
            $this->template->breadcrumbs[] = array($product['name'], url::base() . 'shop/product' . $product['id']);

            //учитываем скидку, курс валют и добавляем к цене банковский код валюты:
            $product['price'] = round($curr * $product['price'] * $pct, 2);
            $product['price'] .= ' ' . $this->currency;
            $product['name'] = htmlspecialchars($product['name']);
            $product['cart'] = FALSE; //по умолчанию продукт не в корзине
            if (is_array($sessionCart) && in_array($product['id'], $sessionCart))
                $product['cart'] = TRUE;

            $this->template->stuff = new View(TPL . 'oneProduct'); //подключаем шаблон для 1 продукта
            $this->template->stuff->comments = $this->boolConfigs['comments'];
            if ($this->boolConfigs['bigCart'])
            {
                if (isset($bcart[$product['id']]))
                    $product['bigcart'] = $bcart[$product['id']];
                elseif (in_array($product['id'], $sessionCart))
                        $product['bigcart'] = 1;
            }

            $this->template->stuff->item = $product; //вставляем в шаблон данные о продукте
            $description = new View(TPL . 'description');
            $description->text = ORM::factory('description', $product['id'])->__get('text');
            if ($this->auth->logged_in('admin'))
                $description->id = $product['id'];
            if (strlen($this->apis['vkcomments']))
            {
                $this->template->css .= View::factory('vk')->set('apiId', $this->apis['vkcomments']);
                $description->set('vk_on', TRUE);
            }
            else
                $description->set('vk_on', FALSE);

            if (strlen($this->apis['disqus']))
                $description->set('disqus_shortname', $this->apis['disqus']);

            $rating = new View(TPL . 'rating');
            $r = ORM::factory('rating_value', $product['id']);
            $rating->val = $r->__get('val');
            $rating->disable = !$this->user;

            if ($this->boolConfigs['comments'])
                $this->template->stuff->set('comments', Model_Comment::form($product['id'], TRUE));
            else
                $this->template->stuff->set('comments', '');

            $this->template->stuff->set('rating', $rating);
            $this->template->stuff->set('description', $description);

            $this->template->title .= ' - ' . $product['name'];
            $this->template->about = ''; //блок приветствия не отображаем

            $this->template->oneProductPage = TRUE; //показывать 'itemscope itemtype="http://schema.org/Product"'

            //Сохраняем просматриваемый пользователем товар для его личной страницы
            if ($this->user)
            {
                try
                {
                    DB::insert('user_views', array('user_id', 'product_id'))
                        ->values(array($this->user->id, $product['id']))->execute();

                } catch (Database_Exception $dbEx)
                { /* Duplicate entry */
                }
            }

        }
        elseif ($cat) //указана категория
        {
            Session::instance()->set('cat', $cat);
            $categories = $this->categoriesObject->getCatChilds($cat);

            if (!count($categories) || !isset($this->categoriesObject->categories['names'][$cat]))
                $this->request->redirect(url::base() . 'error/404');

            $this->navigation_cat($cat);

            $products = Model::factory('product')->byCategory($categories, $page, $this->productsOnPage);
            $productsCount = ORM::factory('product')->where('cat', 'in', $categories)->count_all();
            if (!$productsCount) //нет товаров в категории
                $this->template->stuff = 'В этой категории сейчас нет товаров.';
            else
            {
                $sortSelect = new View(TPL . 'sort');
                $sortSelect->__set('type', Model_Product::getSort());
                $this->template->topBlock2 .= $sortSelect;
                foreach ($products as $k => $p)
                { //учитываем скидку, курс валют и добавляем к цене банковский код валюты:
                    $products[$k]['price'] = round($curr * $p['price'] * $pct, 2) . ' ' . $this->currency;
                    $products[$k]['cart'] = FALSE; //добавляем информацию
                    if (is_array($sessionCart) && in_array($products[$k]['id'], $sessionCart)) //о наличии или отсутствии продукта в корзине
                        $products[$k]['cart'] = TRUE;
                    if ($this->boolConfigs['bigCart']) //если в настройках пользователю разрешено выбирать количество товаров
                    {
                        if (isset($bcart[$p['id']])) //если товара выбрано больше 1 ед.
                            $products[$k]['bigcart'] = $bcart[$p['id']];
                        elseif (in_array($p['id'], $sessionCart)) //товар выбран в количестве 1 ед.
                            $products[$k]['bigcart'] = 1;
                    }
                }
                $this->template->stuff->products = $products; //заполняем его продуктами
                $Pagination = new Pagination(
                    array( //создаем навигацию
                        'uri_segment' => 'page',
                        'total_items' => $productsCount,
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
            $page = isset($_GET['page']) ? abs((int)$_GET['page']) : 1;
            if ($page < 1)
                $page = 1;
            if ($page == 1)
            { //берем 1-ю страницу из кэша
                $products = self::$cache->get('LastProd');
                if (null === $products)
                {
                    $products = Model::factory('product')->getLast($this->productsOnPage);
                    self::$cache->set('LastProd', $products);
                }
            }
            else
                $products = Model::factory('product')->getLast(
                    $this->productsOnPage, ($page - 1) * $this->productsOnPage);

            foreach ($products as $k => $p)
            { //учитываем курс валют, скидку и добавляем к цене банковский код валюты:
                $products[$k]['price'] = round($curr * $p['price'] * $pct, 2) . ' ' . $this->currency;
                $products[$k]['cart'] = FALSE; //добавляем информацию
                $products[$k]['name'] = htmlspecialchars($products[$k]['name']);
                if (in_array($products[$k]['id'], $sessionCart)) //о наличии или отсутствии продукта в корзине
                    $products[$k]['cart'] = TRUE;


                if ($this->boolConfigs['bigCart']) //если в настройках пользователю разрешено выбирать количество товаров
                {
                    if (isset($bcart[$p['id']])) //если товара выбрано больше 1 ед.
                        $products[$k]['bigcart'] = $bcart[$p['id']];
                    elseif (in_array($p['id'], $sessionCart)) //товар выбран в количестве 1 ед.
                            $products[$k]['bigcart'] = 1;
                }
            }
            $this->template->stuff->products = $products; //заполняем представление продуктами

            $Pagination = new Pagination(
                array( //создаем навигацию
                    'uri_segment' => 'products/',
                    'total_items' => ORM::factory('product')->count_all(),
                    'items_per_page' => $this->productsOnPage,
                ));
        }

        if ($this->boolConfigs['bigCart'] && is_object($this->template->stuff)) //указываем опцию из настроек
            $this->template->stuff->bigcart = 1;

        if (isset($Pagination))
            $this->template->stuff .= $Pagination;
    }

    public function action_user()
    {
        if (!$this->user)
            exit($this->request->redirect(url::base()));

        if (isset($_POST['cancel_order']))
        {
            Model_Order::cancel_order_by_user($this->user->id, $_POST['cancel_order']);
            $this->request->redirect($_SERVER['REQUEST_URI']);
        }

        $pct = Model::factory('group')->get_pct($this->user->id); //получаем множитель скидки
        $curr = Model::factory('config')->getCurrency($this->currency); //получение курса валют
        if (!$curr) //если в сессию попал поддельный или уже не существующих в БД курс
        {
            Session::instance()->delete('currency'); //удаляем его из сессии
            $this->request->redirect($_SERVER['REQUEST_URI']); //и обновляем страницу
        }

        $fields = null;
        $fieldVals = null;
        if (ORM::factory('field')->count_all())
        {
            $fields = ORM::factory('field')->find_all();
            $fieldORM = ORM::factory('field_value');
            foreach ($fields as $field)
                $fieldVals[$field->id] = $fieldORM->get($field->id, $this->user->id);
            $fieldVals = $fieldVals;
        }

        $this->template->title .= ' - Личная страница ' . $this->user->username; //дополнение заголовка страницы
        $this->template->about = View::factory(TPL . 'userPage', array(
                // Последние 30 просмотренных товаров
                'views' => Model::factory('Views')->last_products($this->user->id, 30),
                //подставляем данные пользователя
                'user' => $this->user,
                'fields' => $fields,
                'fieldVals' => $fieldVals,
                'adm' => Auth::instance()->logged_in('admin') ? 1 : null,
                'pct' => $pct != 1 ? (100 * (1 - $pct)) . '%' : null,
                // История заказов пользователя
                'orders' => Model::factory('Order')->get_users_order_history(
                        $this->user->id, $pct, $curr, $this->currency),
                'currency' => $this->currency
            ));
    }


    /**
     * Смена валюты
     * @param int $code
     */
    public function action_currency($code = 0)
    { //в настройках машрутизации регулярное выражение [A-Z]{3}
        if ($code) //если параметр <code> установлен
        {
            Session::instance()->set('currency', $code); //записываем в сессию
        }

        if (!Request::$referrer)
        {
            Request::$referrer = url::base();
        }
        //если HTTP_REFERER содержит домен сайта
        if (FALSE === strpos(Request::$referrer, '://' . $_SERVER['HTTP_HOST']))
        {
            Request::$referrer = url::base(); //на главную страницу
        }
        $this->request->redirect(Request::$referrer);
    }

    /**
     * Сортировка в категориях
     * @param int $code
     */
    public function action_sortset($code = 0)
    {
        Session::instance()->set('sort', $code);
        $this->request->redirect(url::base() . 'shop/category' . Session::instance()->get('cat', 1));
    }

    /**
     * Пользователь перешел по партнерской ссылке
     * @param $id
     */
    public function action_referral($id)
    {
        if ($this->boolConfigs['refpp'])
        {
            Session::instance()->set('referral', $id);
        }
        $this->action_index();
    }

    /**
     * Метод перенесен в Controller_Page
     * @param int $id
     */
    public function action_blog($id = 0)
    {
        $this->request->redirect(url::base() . 'blog/' . ($id ? $id : ''));
    }

    /**
     * Метод перенесен в Controller_Page
     */
    public function action_clients()
    {
        $this->request->redirect(url::base() . 'page/clients');
    }

    /**
     * Метод перенесен в Controller_Page
     */
    public function action_contacts()
    {
        $this->request->redirect(url::base() . 'page/contacts');
    }

    /**
     * Метод перенесен в Controller_Order
     */
    public function action_order()
    {
        throw new Exception('Ссылка устарела!');
        $this->request->redirect(url::base() . 'order');
    }

    /**
     * Метод перенесен в Controller_Order
     */
    public function action_cart()
    {
        throw new Exception('Ссылка устарела!');
        $this->request->redirect(url::base() . 'order/cart');
    }

    /**
     * Redirect form "/index.php" to "/"
     */
    public function action_indexphp()
    {
        if (strpos(url::base(), 'index.php') === FALSE)
            $this->request->redirect(url::base());
    }

    public function action_yml()
    {
        header('Content-Type: text/xml; charset=utf-8');
        echo Model_Yml::get();
        exit;
    }

    protected function navigation_cat($cat)
    {
        if ($cat && isset($this->categoriesObject->categories['names'][$cat]))
        {
            $tmp_cat = $cat;
            $parent_cats = array();
            while ($this->categoriesObject->categories['parents'][$tmp_cat])
            {
                $tmp_cat = $this->categoriesObject->categories['parents'][$tmp_cat];
                $parent_cats[] = array(
                    $this->categoriesObject->categories['names'][$tmp_cat],
                    url::base() . 'shop/category' . $tmp_cat
                );
            }
            for ($ind = count($parent_cats) - 1; $ind >= 0; $ind--)
                $this->template->breadcrumbs[] = $parent_cats[$ind];

            $this->template->breadcrumbs[] = array(
                $this->categoriesObject->categories['names'][$cat],
                url::base() . 'shop/category' . $cat
            );
        }
    }
}
