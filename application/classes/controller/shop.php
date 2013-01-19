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
        $page = isset($_GET['page']) ? abs((int)$_GET['page']) : 0; //номер страницы >=0
        if (!$page)
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
                $Pagination = new Pagination(
                    array ( //создаем навигацию
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
                        {//записываем колич. 1
                            $products[$k]['bigcart'] = 1;
                        }
                    }
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

        $this->template->about->user = $this->user; //подставляем данные пользователя
        $this->template->title .= ' - Личная страница ' . $this->user->username; //дополнение заголовка страницы
    }


    /**
     * Смена валюты
     * @param int $code
     */
    public function action_currency($code = 0)
    { //в настройках машрутизации регулярное выражение [A-Z]{3}
        if ($code) //если параметр <code> установлен
        {
            Session::instance()->set('currency', $code);//записываем в сессию
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
        $this->request->redirect(url::base() . 'blog/' . ($id ? $id : '' ));
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
        if(strpos(url::base(),'index.php') === FALSE)
            $this->request->redirect(url::base());
    }

    /*
    public function action_register()
    {
        $this->template->title .= ' - Регистрация'; //дополнение заголовка страницы
        if ($this->auth->get_user()) //если пользователь уже авторизован,
        {
            exit($this->request->redirect(url::base()));//перенаправим на главную страницу.
        }

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
                $errStr .= $key . $value . '. ';//вместе с ключами массива
            }
        }

        //подключение в шаблон данных, которые были введены в прошлый раз (если тогда были ошибки)
        $this->template->about->val = Session::instance()->get('register_post');

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

    }*/
}
