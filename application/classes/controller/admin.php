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

class Controller_Admin extends Controller_Template
{

    public $template = 'admin/index'; //общее представление для контроллера

    /**
     * функция выполняется перед каждым action
     */
    public function before()
    {
        parent::before();

        Auth::instance()->auto_login();
        if (!Auth::instance()->logged_in('admin'))
            die(Request::factory(url::base() . 'error/404')->execute());

        $this->template->path = url::base() . 'admin/';
        $this->template->title = 'Панель управления ';
        $this->template->head = '';
        $this->template->body = '';

        if (isset($_POST) && count($_POST))
            if (!isset($_SERVER['HTTP_REFERER'])
                || strpos($_SERVER['HTTP_REFERER'], 'ttp://' . $_SERVER['HTTP_HOST']) !== 1
            )
                die(Request::factory(url::base() . 'error/xsrf')->execute());

    }

    /**
     * Страница 'Активные заказы'
     */
    public function action_index()
    {
        $this->template->title .= '- Активные заказы';
        $this->template->body = new View('admin/table/activeOrders');

        if (isset($_GET['set_order_desc']))
        {
            $this->template->body->sortOrder = (bool)$_GET['set_order_desc'];
            Cookie::set('admin_sort_orders_desc', $this->template->body->sortOrder, DATE::YEAR);
        }
        else
        {
            $this->template->body->sortOrder = Cookie::get('admin_sort_orders_desc', false);
        }

        $state = ORM::factory('state_order')
            ->find_all()
            ->as_array('id', 'name');

        if (!isset($_POST['search']))
        {
            $orders = ORM::factory('order')
                ->where('status', '!=', 5)
                ->and_where('status', '!=', 4)
                ->order_by('id', $this->template->body->sortOrder ? 'desc' : 'asc')
                ->find_all()
                ->as_array();

            if (count($orders))
            {
                $this->template->body->array = array();

                foreach ($orders as $k => $item)
                {
                    $this->template->body->array[$k]['id'] = $item->id;
                    if ($item->user)
                    {
                        $this->template->body->array[$k]['user_id'] = $item->user;
                        $this->template->body->array[$k]['username'] = htmlspecialchars($item->username);
                    }

                    $this->template->body->array[$k]['address'] = nl2br(htmlspecialchars($item->address));

                    $this->template->body->array[$k]['phone'] = htmlspecialchars($item->phone);
                    $this->template->body->array[$k]['contacts'] = nl2br(htmlspecialchars($item->contacts));
                    $this->template->body->array[$k]['status']
                        = isset($state[$item->status]) ? $state[$item->status] : 'название этого статуса удалено';
                    $this->template->body->array[$k]['else_status'] = array();
                    foreach ($state as $key => $st)
                        if ($key != $item->status)
                            $this->template->body->array[$k]['else_status'][] = $st;

                    $this->template->body->array[$k]['date'] = date('d.m.Y H:I:s', $item->date);
                    $difference = time() - $item->date;
                    $hours = (int)($difference / 3600);
                    $minuts = (int)(($difference - $hours * 3600) / 60);
                    $this->template->body->array[$k]['difference'] = $hours . 'час. ' . $minuts . 'мин.';
                }
            }
        }
        else
        {
            $search = ORM::factory('order', $_POST['search'])->as_array();
            if ($search['id'])
            {
                if ($search['user'])
                {
                    $search['user_id'] = $search['user'];
                    unset($search['user']);
                    $search['user_name'] = ORM::factory('user', $search['user_id'])->__get('username');
                }
                if (!isset($state[$search['status']]))
                    $search['status'] = 'название этого статуса удалено';
                else
                    $search['status'] = $state[$search['status']];
                $search['else_status'] = array();
                foreach ($state as $key => $st)
                    if ($key != $search['status'])
                        $search['else_status'][] = $st;
                //print_r($search);
                $search['date'] = date('d.m.Y H:I:s', $search['date']);
                $difference = time() - $search['date'];
                $hours = (int)($difference / 3600);
                $minuts = (int)(($difference - $hours * 3600) / 60);
                $search['difference'] = $hours . 'час. ' . $minuts . 'мин.';
                $this->template->body->array = array($search);
            }
            else
                $this->template->body->search_error = 'Заказ не найден.';
        }

        $last = ORM::factory('order') //последний заказ
            ->order_by('id', 'desc')
            ->limit(1)
            ->find()
            ->as_array();
        $difference = time() - $last['date']; //прошло секунд с последнего заказа
        $hours = (int)($difference / 3600); //прошло часов
        $minuts = (int)(($difference - $hours * 3600) / 60); //прошло минут с вычитанием целых часов
        $count_all = ORM::factory('order')->count_all(); //всего заказов
        $count_bad = ORM::factory('order')->where('status', '=', 4)->count_all(); //ложных заказов
        $for30days = ORM::factory('order') //заказов за последние 2592000 сек. == 30 дней
            ->where('date', '>', time() - 2592000)
            ->count_all();
        $info = new View('admin/info');
        if ($count_all) //избежание деления на 0
        {
            $info->info = 'Последний заказ был ' . $hours . 'час. ' . $minuts .
                'мин. назад.' . '<br>Заказов за последние 30 дней: ' .
                $for30days . '<br> Всего было заказов: <span id="ordcount">'
                . $count_all . '</span><br>из которых ложных: '
                . $count_bad . ' (' .
                round(100 * $count_bad / $count_all) . '%)';

            $this->template->body = $info . $this->template->body;
        }


    }

    /**
     * Страница редактирования профилей пользователей
     * @param int $id
     */
    public function action_user($id = null)
    {
        $user = ORM::factory('user', $id);
        if ($id && $user->id)
        {
            $edit = new View('themes/default/userPage');
            $edit->user = $user;
            $this->template->title .= '- Редактирование профиля ' . $user->id;
            $edit->adm = TRUE;
            $groups = array();
            $gr = ORM::factory('groups_user', $id);

            $allGroups = ORM::factory('group')->find_all();
            if ($gr->gid)
            {
                foreach ($allGroups as $group)
                    if ($group->id == $gr->gid)
                        $groups[0] = array('id' => $gr->gid, 'name' => $group->name);
                foreach ($allGroups as $group)
                    if ($group->id != $gr->gid)
                        $groups[] = array('id' => $group->id, 'name' => $group->name);
                $groups[] = array('id' => -1, 'name' => '-');
            }
            else
            {
                $groups[0] = array('id' => -1, 'name' => '-');
                foreach ($allGroups as $group)
                    $groups[] = array('id' => $group->id, 'name' => $group->name);
            }

            $edit->groups = $groups;
            $edit->is_admin = $user->is_admin();

            if (ORM::factory('field')->count_all())
            {
                $edit->fields = ORM::factory('field')->find_all();
                $fieldORM = ORM::factory('field_value');
                foreach ($edit->fields as $field)
                    $fieldVals[$field->id] = $fieldORM->get($field->id, $id);
                $edit->fieldVals = $fieldVals;
            }


            $info = new View('admin/info');

            $info->info = 'Последний визит ';
            $info->info .= date('d.m.Y H:I:s', $user->last_login);
            $info->info .= '<br>Заказов: ';
            $info->info .= ORM::factory('order')
                ->where('user', '=', $id)
                ->count_all();
            $info->info .= '<br>Ложных заказов: ';
            $info->info .= ORM::factory('order')
                ->where('user', '=', $id)
                ->and_where('status', '=', 4)
                ->count_all();

            $this->template->body = $info . $edit;
        }
        else
        {
            if (isset($_POST['name']) && isset($_POST['type']))
            { //сохранение нового поля
                $newField = ORM::factory('field');
                $newField->__set('name', $_POST['name']);
                $newField->__set('type', $_POST['type']);
                if (isset($_POST['empty']))
                    $newField->__set('empty', 1);
                $newField->save();
                $this->request->redirect(url::base() . 'admin/user');
            }

            $this->template->title .= '- Клиенты';
            $info = new View('admin/info');
            $last = new View('admin/findUser');
            $last->users = ORM::factory('user')
                ->order_by('id', 'desc')
                ->limit(10)
                ->find_all();
            $last->types = ORM::factory('field_type')->find_all();
            $last->fields = ORM::factory('field')->find_all();

            $this->template->head = new View('admin/ckeditorHeader');
            if (isset($_POST['title']) && isset($_POST['editor']) && isset($_POST['time']))
                Model::factory('send_email')
                    ->send($_POST['title'], $_POST['editor'], time() - $_POST['time'] * 24 * 60 * 60);

            $clients = ORM::factory('user')->count_all();

            $admins = ORM::factory('Roles_user')->where('role_id', '=', 2)->count_all();

            $orders0 = ORM::factory('order')->where('status', '=', 5)->find_all(); //все заказы со статусом 5 (выполнено)
            $arrayUsers = array();
            foreach ($orders0 as $ord)
                if ($ord->user)
                    $arrayUsers[$ord->user] = 0; //массив с пользователями в ключах (без повторов)
            foreach ($arrayUsers as $usr => $null)
                foreach ($orders0 as $ord)
                    if ($ord->user == $usr)
                        $arrayUsers[$usr]++; //в значения массива записываются количества покупок пользователей
            arsort($arrayUsers);

            $arrayUsers = array_keys($arrayUsers);
            $arrayUsers = array_slice($arrayUsers, 0, 10);
            $last->bestUsers = array();
            foreach ($arrayUsers as $usr)
                $last->bestUsers[] = ORM::factory('user', $usr);

            $info->info = 'Клиентов: ' . ($clients - $admins);
            $info->info .= '<br>Администраторов: ' . $admins;
            $info->info .= '<br>Заказов: ' . ORM::factory('order')->count_all();
            $this->template->body = $info . $last;
        }
    }

    /**
     * Страница редактирования некоторых настроек модулей магазина
     */
    public function action_config()
    {
        $this->template->title .= '- Настройки';
        $this->template->body = new View('admin/config');
        $bool = Model::factory('config')->getBool();
        $this->template->body->bool = $bool;
        $this->template->body->email = ORM::factory('mail', 1)->__get('value');
        $this->template->body->jabber = ORM::factory('mail', 2)->__get('value');
        $this->template->body->email3 = ORM::factory('mail', 3)->__get('value');
        $this->template->body->shopName = ORM::factory('html')->getblock('shopName');
        $this->template->body->keywords = ORM::factory('html')->getblock('keywords');
        $this->template->body->menu = Model::factory('menuItem')->get();
        $this->template->body->status = ORM::factory('state_order')->find_all()->as_array();
        $this->template->body->analytics = Model_Apis::get('analytics');
        $this->template->body->sape = Model_Apis::get('sape');
        $this->template->body->disqus = Model_Apis::get('disqus');
        $this->template->body->vkcomments = Model_Apis::get('vkcomments');

        if (isset($_POST['question']))
            Model::factory('poll')->set(strip_tags($_POST['question']));

        if (isset($_POST['answers']))
        {
            $answers = array();
            foreach (explode("\n", $_POST['answers']) as $i => $answer)
                if (trim($answer))
                    $answers[] = trim($answer);
            if (count($answers))
            {
                DB::delete('poll_answers')->execute();
                foreach ($answers as $answer)
                {
                    $a = ORM::factory('poll_answer');
                    $a->text = $answer;
                    $a->save();
                }
            }
        }

        $this->template->body->question = Model::factory('poll')->get();
        $this->template->body->answers = ORM::factory('poll_answer')->find_all();
        if (isset($_POST['clearRating']))
        {
            foreach (ORM::factory('Rating_value')->find_all() as $find)
                $find->delete();
            foreach (ORM::factory('Rating_user')->find_all() as $find)
                $find->delete();
        }
    }

    /**
     * Страница редактирования валют
     */
    public function action_curr()
    {
        if (isset($_POST['update_from_cbr']) && FALSE === Model::factory('cbr')->update_local_currencies())
        {
            Session::instance()->set('error_adm', 'Обновить не удалось. Попробуйте позже или сообщите разработчику.');
            $this->request->redirect($_SERVER['REQUEST_URI']);
        }

        $this->template->title .= '- Валюты';
        $this->template->body = View::factory('admin/info', array(
                'info' => 'Валюта по умолчанию - ' . DEFAULT_CURRENCY .
                    '.<br> Изменить ее можно в ' . APPPATH . 'bootstrap.php ' .
                    'в строке ' . (LINE_CURR_CHANGE - 1) . '<br>' .
                    'Слева код валюты, а справа число,
                         на которую будут умножатся цены при выборе этой валюты. <br>
                         Если множитель валюты равен 1 - все цены сохранены в этой валюте.'))
            . View::factory('admin/editCurr', array(
                'array' => Model_Config::getCurrency(),
                'errors' => Session::instance()->get('error_adm', '')
            ));
        Session::instance()->delete('error_adm');
    }

    /**
     * Страница управления категориями товаров
     */
    public function action_categories()
    {
        $this->template->title .= '- Категории товаров';
        $info = new View('admin/info');
        $info->info = 'Это страница управления категориями товаров.<br> Выберите категорию кликом мыши';
        $info->info .= new View('admin/lastCatId');
        $categories = new Categories(); //инициализация класса для построения дерева категорий
        $this->template->body = new View('admin/categories');
        $this->template->body->cats = $categories->menu('javascript:void(0);#');
        $this->template->body->countCats = count($categories->categories['names']);

        $this->template->body->errors = Session::instance()->get('error_adm');
        Session::instance()->delete('error_adm');
        $this->template->head = '';
        if (isset($_POST['editor']) && $_POST['editor'])
            $this->template->body->html = $_POST['editor'];
        elseif (isset($_POST['ed']) && $_POST['ed'] > 0)
        {
            $this->template->body->html = Model_Descr_cat::get($_POST['ed']);
            $this->template->head = new View('admin/ckeditorHeader');
        }
        else
            $this->template->body->html = '';

        $this->template->body = $info . $this->template->body;
        if (isset($_POST['descrid']) && isset($_POST['editor']))
            Model_Descr_cat::set((int)$_POST['descrid'], $_POST['editor']);

    }

    /**
     * Страница управления группами скидок
     */
    public function action_groups()
    {
        $this->template->title .= '- Группы скидок';
        $this->template->body = new View('admin/groups');
        $info = new View('admin/info');
        $info->info = 'Вы можете добавлять пользователей в группы скидок.<br> Это страница редактирования групп.';
        $this->template->body->groups = ORM::factory('Group')->find_all();
        $this->template->body->errors = Session::instance()->get('error_adm');
        Session::instance()->delete('error_adm');
        $this->template->body = $info . $this->template->body;
    }

    /**
     * Страница управления товарами
     */
    public function action_products($cat = 0)
    {
        $onPage = 200;
        if (isset($_POST['add_item']))
        {   //Добавление товаров по 1
            ORM::factory('product')
                ->set('name', str_replace(' ', '-', strtolower($_POST['add_item'])))
                ->set('name', $_POST['add_item'])
                ->save();
            $this->request->redirect(
                url::base()
                . 'admin/products?page='
                . ceil(Model::factory('product')->count_all() / $onPage)
                . '#addItemForm');
            Model::factory('sitemap')->update();
            exit;
        }

        $this->template->title .= '- Товары';

        $this->template->body = new View('admin/table/products');
        $info = new View('admin/info');
        $info->info = 'Вы можите добавлять продукты используя электронные таблицы xls (Microsoft Office Excel или OpenOffice) <br>
            Размещайте столбцы таблиц в такой последовательности: <br> 
            id_товара, id_категории, название_товара, подробное_описание, цена, URL_фотографий_через_пробел, количество_на_складе.<br>  
            Первый столбец содержит целочисленное id товара, 
            которое необходимо при массовом обновлении информации о товарах 
            и определяет адрес страницы товара. Если его оставить пустым, то он будет заполнятся по порядку.<br>
            Количество на складе - по умолчанию 1.<br>
            Пример xls файла можете скачать по <a href="' . url::base() . 'example.xls">этой ссылке</a>, а
            узнать id любой категории - по <a href="' . $this->template->path . 'categories">этой</a>';
        $this->template->body->currency = Model_Config::get1Currency();
        $categories = new Categories();
        $this->template->body->select = $categories->select($cat);

        $page = isset($_GET['page']) ? abs((int)$_GET['page']) : 0; //получение GET параметра page >= 0
        if (!$page) //если он равен 0
            $page = 1; //устанавливаем в 1
        $cats = ORM::factory('product');
        if ($cat)
            $cats->where('cat', '=', $cat);
        $cats->limit($onPage)
            ->offset(($page - 1) * $onPage);
        $cats = $cats->find_all();

        $countAll = ORM::factory('product');
        if ($cat)
            $countAll->where('cat', '=', $cat);

        $countAll = $countAll->find_all()->count();
        $this->template->body->cats = array();
        foreach ($cats as $c)
        {
            $c->cat = $categories->select($c->cat);
            $this->template->body->cats[] = $c;

        }
        $this->template->body->info = $info;
        if (Session::instance()->get('import'))
        {
            $this->template->body->import = Session::instance()->get('import');
            Session::instance()->delete('import');
        }
        $this->template->body .= new Pagination(array(
            'uri_segment' => 'page',
            'total_items' => $countAll,
            'items_per_page' => $onPage,
        ));
    }

    /**
     * Редактирование HTML блоков
     * @param int $block
     */
    public function action_edit($block = 0)
    {
        $this->template->head = new View('admin/ckeditorHeader');
        $this->template->body = new View('admin/editHTML');
        if (isset($_POST['editor']))
        {
            switch ($block)
            {
                case 1:
                    Model::factory('html')->setblock('about', $_POST['editor']);
                    break;
                case 2:
                    Model::factory('html')->setblock('banner1', $_POST['editor']);
                    break;
                case 3:
                    Model::factory('html')->setblock('banner2', $_POST['editor']);
                    break;
                case 4:
                    Model::factory('html')->setblock('banner3', $_POST['editor']);
                    break;
                case 5:
                    Model::factory('html')->setblock('banner4', $_POST['editor']);
                    break;
                case 6:
                    Model::factory('html')->setHtml(1, $_POST['editor']);
                    break;
                case 7:
                    Model::factory('html')->setHtml(2, $_POST['editor']);
                    break;
                case 8:
                    Model::factory('html')->setblock('logo', $_POST['editor']);
                    break;
                case 9:
                    Model::factory('html')->setblock('topTitle', $_POST['editor']);
                    break;
                default:
                    break;
            }
            Cache::instance()->delete('htmlBlocks');
        }
        switch ($block)
        {
            case 1:
                $this->template->body->text = Model::factory('html')->getblock('about');
                break;
            case 2:
                $this->template->body->text = Model::factory('html')->getblock('banner1');
                break;
            case 3:
                $this->template->body->text = Model::factory('html')->getblock('banner2');
                break;
            case 4:
                $this->template->body->text = Model::factory('html')->getblock('banner3');
                break;
            case 5:
                $this->template->body->text = Model::factory('html')->getblock('banner4');
                break;
            case 6:
                $this->template->body->text = Model::factory('html')->getHtml(1);
                break;
            case 7:
                $this->template->body->text = Model::factory('html')->getHtml(2);
                break;
            case 8:
                $this->template->body->text = Model::factory('html')->getblock('logo');
                break;
            case 9:
                $this->template->body->text = Model::factory('html')->getblock('topTitle');
                break;
            default:
                $this->template->body->text = '';
                break;
        }

    }

    /**
     * Блог
     * @param int $edit
     */
    public function action_blog($edit = 0)
    {
        $this->template->head = new View('admin/ckeditorHeader');
        if (!$edit) //добавление записи в блог
        {
            $this->template->body = new View('admin/add2blog');
            $this->template->body->errors = '';
            if (isset($_POST['editor']) && isset($_POST['title']))
            {
                $errors = Model::factory('BlogPost')->__add(array('title' => $_POST['title'], 'html' => $_POST['editor'], 'html2' => $_POST['editor2']));
                foreach ($errors as $field => $error)
                    $this->template->body->errors .= $field . $error . '. ';
                if ($this->template->body->errors)
                {
                    $this->template->body->errors = str_replace('title', 'Заголовок', $this->template->body->errors);
                    $this->template->body->errors = str_replace('html2', 'Сокращенный вариант', $this->template->body->errors);
                    $this->template->body->errors = str_replace('html', 'Текст', $this->template->body->errors);
                    $this->template->body->post = array('title' => $_POST['title'], 'code' => $_POST['editor'], 'code2' => $_POST['editor2']);
                }
                else
                {
                    $this->template->body->errors = '<a href="' . url::base() .
                        'blog/' . Model_LastInsert::id() .
                        '">Добавлено!</a>';
                    Model::factory('BlogPost')->updateFeed('/rss.xml'); //обновление RSS ленты
                    Model::factory('sitemap')->update(); //обновление sitemap
                }
            }
        }
        else
        {
            if (!isset($_POST['remove'])) //редактирование
            {
                $this->template->body = new View('admin/editBlog');
                $this->template->body->errors = '';

                if (isset($_POST['title']) && isset($_POST['editor']))
                {
                    $errors = Model::factory('BlogPost')->__update(array(
                        'title' => html_entity_decode($_POST['title']),
                        'html' => $_POST['editor'],
                        'html2' => $_POST['editor2'],
                        'id' => $edit
                    ));
                    foreach ($errors as $field => $error)
                        $this->template->body->errors .= $field . $error . '. ';
                    if ($this->template->body->errors)
                    {
                        $this->template->body->errors = str_replace('title', 'Заголовок', $this->template->body->errors);
                        $this->template->body->errors = str_replace('html2', 'Сокращенный вариант', $this->template->body->errors);
                        $this->template->body->errors = str_replace('html', 'Текст', $this->template->body->errors);

                    }
                    else
                    {
                        Model::factory('BlogPost')->updateFeed('/rss.xml');
                        $this->template->body->errors = '<a href="' . url::base() .
                            'blog/' . $edit . '">Обновлено!</a>';
                    }

                }
                $this->template->body->post = ORM::factory('BlogPost', $edit)->as_array();
                if (!isset($this->template->body->post['title']))
                    die($this->request->redirect(url::base() . 'shop/blog'));
                $this->template->body->post['title'] = htmlspecialchars($this->template->body->post['title']);
            }
            else //удаление
            {
                ORM::factory('BlogPost', $edit)->delete();
                $this->request->redirect(url::base() . 'blog/');
                Model::factory('sitemap')->update();
                Model::factory('BlogPost')->updateFeed('/rss.xml');
            }
        }
    }

    public function action_description($id = null)
    {
        $p = ORM::factory('product', $id);
        $productname = $p->__get('name');
        if (!$productname)
            die(Request::factory('error/404')->execute());

        $this->template->body = new View('admin/description');
        $this->template->body->productname = $productname;
        $this->template->body->link = $p->getUri();
        $this->template->head = new View('admin/ckeditorHeader');

        if (isset($_POST['editor']))
        {
            $model = ORM::factory('description', $id);
            if (!$model->id)
            {
                $model = ORM::factory('description');
                $model->__set('id', $id);
            }

            $model->__set('text', $_POST['editor']);
            $model->save();

        }
        $this->template->body->html = htmlspecialchars(ORM::factory('description', $id)->__get('text'));
    }

    public function action_paytypes($id = null)
    {
        $page_update_url = $this->template->path . 'paytypes/' . (string)((int)$id);
        if (isset($_POST['ik_secret_key'], $_POST['ik_shop_id']))
        {
            Model_Apis::set('ik_secret_key', $_POST['ik_secret_key']);
            Model_Apis::set('ik_shop_id', $_POST['ik_shop_id']);
            $this->request->redirect($page_update_url);
        }

        if (isset($_POST['editor']) && isset($_POST['newname']))
        {
            if ($id) //редактирование
            {
                $change = ORM::factory('pay_type', $id);
                $change->__set('name', $_POST['newname']);
                $change->__set('text', $_POST['editor']);
                $change->save();
            }
            else //добавление
            {
                $change = ORM::factory('pay_type');
                $change->__set('name', $_POST['newname']);
                $change->__set('text', $_POST['editor']);
                $change->__set('active', 1);
                $change->save();
            }
            $this->request->redirect($page_update_url);
        }

        if (isset($_POST['del'])) //удаление
        {
            ORM::factory('pay_type', $id)->delete();
            $this->request->redirect($page_update_url);
        }
        //смена статуса (вкл\выкл)
        if (isset($_POST['id']) && isset($_POST['checked']))
        {
            $change = ORM::factory('pay_type', $_POST['id']);
            $change->__set('active', (int)$_POST['checked']);
            $change->save();
            exit;
        }

        $this->template->body = new View('admin/payTypes');
        $this->template->body->types = ORM::factory('pay_type')->find_all();
        $this->template->head = new View('admin/ckeditorHeader');
        $this->template->body->path = $this->template->path;
        $info = new View('admin/info');
        $info->info = 'Вы можете использовать переменные {{sum}}, {{refpp}} и {{id}}.' .
            '<br> {{sum}} - сумма заказа.' .
            '<br> {{refpp}} - баланс в партнерской программе.' .
            '<br> {{id}} - уникальный номер заказа';

        foreach ($this->template->body->types as $type)
            if ($id == $type->id)
            {
                $this->template->body->type4edit = $type;
                if ($id == 4)
                {
                    $this->template->body->interkassa = $info
                        . View::factory('admin/interkassa', array(
                            'ik_secret_key' => Model_Apis::get('ik_secret_key'),
                            'ik_shop_id' => Model_Apis::get('ik_shop_id')
                        ));
                    return;
                }
                break;
            }
        $this->template->body .= $info;
    }

    //удаление комментария
    public function action_deletecomment($id = 0)
    {
        $id = (int)$id;
        $error = 'Вы должны перейти сюда по ссылке со страницы с комментарием.
            Если это так, проверьте, не меняет ли браузер HTTP_REFERER';
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : die($error);

        if (
            (preg_match('|/product/|', $referer) OR preg_match('|/blog/[0-9]+|', $referer))
            &&
            FALSE !== strpos($referer, $_SERVER['HTTP_HOST'])
            &&
            $id
        ) //удаление комментария
            ORM::factory('comment', $id)->delete();
        else
            die($this->request->redirect(url::base()));

        die($this->request->redirect($referer));
    }

    public function action_images($id = 0)
    {
        if (!(bool)(int)$id)
            return;

        $id = (int)$id;
        //находим товар, для которого изображение
        $product = ORM::factory('product')->find($id)->as_array();
        if (!isset($product['id']))
            return;

        $bigUrl = url::base()
            . 'images/products/' . $id;
        $smallUrl = url::base()
            . 'images/products/small/' . $id;

        $bigFile = $_SERVER['DOCUMENT_ROOT'] . $bigUrl;
        $smallFile = $_SERVER['DOCUMENT_ROOT'] . $smallUrl;

        //собираем список файлов, которые есть для этого товара
        $files = array();
        if (file_exists($bigFile . '.jpg') && file_exists($smallFile . '.jpg'))
            $files[0] = array($bigUrl . '.jpg', $smallUrl . '.jpg');
        $i = 1;
        while (file_exists($bigFile . '-' . $i . '.jpg')
            && file_exists($smallFile . '-' . $i . '.jpg'))
        {
            $files[$i] = array(
                $bigUrl . '-' . $i . '.jpg',
                $smallUrl . '-' . $i . '.jpg'
            );
            $i++;
        }

        //Загрузка картинки
        if (isset($_FILES['img']))
        {
            $file = APPPATH . 'cache/' . text::random('alnum', 12) . '.jpg'; //временный файл
            if (!move_uploaded_file($_FILES['img']['tmp_name'], $file))
                die('Ошибка загрузки');


            if (!ORM::factory('saveImage')->gdFile($file, count($files) ? "$id-$i" : $id))
            {
                unlink($file);
                die('Not an image or invalid image! <br><a href="'
                    . url::base() . 'admin/images/' . $id . '">back</a>');
            }
            unlink($file);
            die($this->request->redirect('admin/images/' . $id));
        }

        //Удаление одной из картинок
        if (isset($_POST['deletePic']))
        {
            $del_i = (int)$_POST['deletePic'];
            foreach ($files as $i => $nl)
            {
                if ($i == $del_i)
                {
                    if ($i == 0)
                    { //удаляем первую картинку
                        unlink($bigFile . '.jpg');
                        unlink($smallFile . '.jpg');
                    }
                    else
                    { //удаляем не первую по порядку картинку
                        unlink($bigFile . '-' . $i . '.jpg');
                        unlink($smallFile . '-' . $i . '.jpg');
                    }
                }
                elseif ($i > $del_i && $i > 1)
                {
                    //Картинки следующие за удаляемым необходимо поднять на 1 i вверх
                    rename($bigFile . '-' . $i . '.jpg', $bigFile . '-' . ($i - 1) . '.jpg');
                    rename($smallFile . '-' . $i . '.jpg', $smallFile . '-' . ($i - 1) . '.jpg');
                }
                elseif ($i > $del_i && $i == 1)
                {
                    rename($bigFile . '-' . $i . '.jpg', $bigFile . '.jpg');
                    rename($smallFile . '-' . $i . '.jpg', $smallFile . '.jpg');
                }
            }

            die($this->request->redirect('admin/images/' . $id));
        }

        $this->template->body = new View('admin/images');
        $this->template->body->product = $product;
        $this->template->body->files = $files;
    }

    public function action_meta()
    {
        $errorStr = Session::instance()->get('meta_err', '');
        Session::instance()->delete('meta_err');

        if (isset($_POST['update'], $_POST['id'], $_POST['title'], $_POST['description'], $_POST['keywords']))
        {
            // updating row
            $m = ORM::factory('meta', $_POST['id']);
            if (!$m->id)
                die('error');
            foreach (array('title', 'description', 'keywords') as $k)
                $m->$k = $_POST[$k];
            $m->save();
            die('ok');
        }
        elseif (isset($_POST['del']))
        {
            // deleting row
            ORM::factory('meta')->delete($_POST['del']);
            die('ok');
        }
        elseif (isset($_POST['new_save'], $_POST['path'], $_POST['title'], $_POST['description'], $_POST['keywords']))
        {
            // inserting new row or updating if found
            $m = ORM::factory('meta')->where('path', '=', $_POST['path'])->find();
            if ($m->id)
            {
                $errorStr .= 'Страница с таким URI уже была в базе, ее значения мета-тегов обновлены. ';
            }
            else
            {
                $m = ORM::factory('meta');
                $m->path = $_POST['path'];
            }
            foreach (array('title', 'description', 'keywords') as $k)
                $m->$k = $_POST[$k];
            if ($_POST['path'] && $_POST['path'][0] == '/')
                $m->save();
            else
                $errorStr .= 'Страница не сохранена. Адрес должен начинаться с символа / ';

            if ($errorStr)
                Session::instance()->set('meta_err', $errorStr);

            $this->request->redirect($_SERVER['REQUEST_URI']);
        }

        $perPage = 10;
        $page = isset($_GET['page']) ? abs((int)$_GET['page']) : 1;
        if (!$page)
            $page = 1;
        if (isset($_GET['search_path']))
        {
            $countAll = 0;
            $meta = ORM::factory('meta')->where('path', '=', $_GET['search_path'])->find_all();
        }
        else
        {
            $countAll = ORM::factory('meta')->count_all();
            $meta = ORM::factory('meta')->limit($perPage)->offset(($page - 1) * $perPage)->find_all();
        }

        $this->template->body = View::factory('admin/meta', array(
            'meta' => $meta,
            'error' => $errorStr,
            'pagination' => new Pagination(array(
                'uri_segment' => 'page',
                'total_items' => $countAll,
                'items_per_page' => $perPage,
            ))
        ));
    }

}//end Controller_Admin