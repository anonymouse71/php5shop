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
 * Контроллер для обработки данных, принятых через AJAX
 */

class Controller_Ajax extends Controller
{
    public function before()
    {
        parent::before();
        if (!Auth::instance()->logged_in())
            Auth::instance()->auto_login();

        if (isset($_POST) && count($_POST))
            if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'ttp://' . $_SERVER['HTTP_HOST']) !== 1)
                die(Request::factory(url::base() . 'error/xsrf')->execute());
    }

    /**
     * Сохранение данных профиля пользователя
     *
     */
    public function action_user()
    {
        $auth = Auth::instance();

        if (!$auth->logged_in() || !isset($_POST)) //пользователь не авторизован или зашел на страницу напрямую
        {
            die('Авторизуйтесь');
        }

        $admin = $auth->logged_in('admin');

        //массив необходимых ключей массива POST
        $postKeys = array('phone', 'address', 'id', 'email');
        //POST содержит не все ключи => запрос отправлен с другого сайта
        foreach ($postKeys as $key)
            if (!isset ($_POST[$key]))
                die(Request::factory(url::base() . 'error/xsrf')->execute());

        $user = $auth->get_user();

        $userId = $user->__get('id'); //получаем id
        if ($admin && $_POST['id'] != $userId)
            $id = $_POST['id'];
        else
            $id = $userId;

        $user = Model::factory('user', $id);

        if ($id != $_POST['id'] && !$admin) //пользователь отправил не свой id и он не админ (hack attempt)
            die(Request::factory(url::base() . 'error/xsrf')->execute());

        if (ORM::factory('field')->count_all()) //если есть дополнительные поля
            foreach (ORM::factory('field')->find_all() as $field)
                if (isset($_POST['f' . $field->id]) && !empty($_POST['f' . $field->id]))
                {
                    if (ORM::factory('field')->validate($_POST['f' . $field->id], $field->type))
                        Model::factory('field_value')->set($field->id, $id, $_POST['f' . $field->id]);
                    else
                        echo $field->name . ': поле введено в неправильном формате. ';
                }
                else
                {
                    if ($field->empty)
                        Model::factory('field_value')->set($field->id, $id, '');
                    else
                        echo $field->name . ': поле не должно быть пустым. ';
                }

        if ($_POST['phone']) //телефон введен?
            echo Model::factory('user')->set_phone($id, (string)$_POST['phone']);
        //устанавливаем
        if ($_POST['address']) //адрес введен?
            echo Model::factory('user')->set_address($id, $_POST['address']); //устанавливаем

        if ($_POST['email']) //редактирование email
        {
            echo $user->set_email($id, $_POST['email']);
        }
        else
            echo ' Email не указан.';

        if ($_POST['username']) //редактирование username
        {
            echo $user->set_username($id, $_POST['username']);
        }
        else
            echo ' Имя не указано.';

        if ($admin)
        {
            if (isset($_POST['gid'])) //редактирование группы
            {
                $gid = (int)$_POST['gid'];
                $gr = ORM::factory('groups_user', $_POST['id']);
                if ($gid > 0 && $gid < 99)
                {
                    if (!$gr->id)
                        $gr->id = $_POST['id'];
                    $gr->__set('gid', (int)$_POST['gid']);
                    $gr->save();
                    echo 'Группа сохранена. ';
                }
                else
                {
                    $gr->delete();
                    echo 'Не состоит в группах. ';
                }
            }
            if (isset($_POST['is_admin']))
            {
                if ($_POST['is_admin'] == 1 && !$user->is_admin())
                    $user->make_admin();
                if ($_POST['is_admin'] == 2)
                    $user->make_not_admin();
            }
        }

    }

    /**
     * Удаляет из сессии товары с заданным id
     * @param int $id
     */
    public function action_delfromcart($id = 0)
    {
        $cart = Session::instance()->get('cart');
        $bigCart = Session::instance()->get('bigCart');
        if (isset($bigCart[$id]))
        {
            unset($bigCart[$id]);
            Session::instance()->set('bigCart', $bigCart);
        }

        if (is_array($cart))
            foreach ($cart as $c)
                if ($c != $id)
                    $newArray[] = $c;

        if (isset($newArray))
            Session::instance()->set('cart', $newArray);
        else
            Session::instance()->set('cart', array());

        echo json_encode(array('sum' => Model_Ordproduct::sum()));
    }

    /**
     * Добавление в корзину товара $id в количестве $count
     * @param int $id
     * @param int $count
     */
    public function action_add_to_cart($id = 0, $count = 0)
    {
        if ($id)
        {
            if (ORM::factory('product', $id)->__get('id'))
            {
                $cart = Session::instance()->get('cart');
                $cart[] = $id;
                Session::instance()->set('cart', $cart);
                if ($count)
                {
                    $bigCart = Session::instance()->get('bigCart');
                    $bigCart[$id] = $count;
                    Session::instance()->set('bigCart', $bigCart);
                }
            }
        }
    }

    /**
     * Поиск по продуктам. Ответ в формате json.
     * Отображаются только первые 20 (для снижения нагрузки на сервер)
     * @param string $string
     */
    public function action_search($string = null)
    {
        if ($string)
        {
            //$string2 = preg_replace('#[-.\'"(),/\;:<>]*#u', '', $string);     //без осн. спецсимв.
            $string2 = $string; //регулярное выражение заменено на более быстрый варинт с str_replace
            foreach (array('=', '.', ';', ':', '\\', "'", '"', '(', ')', '/', '<', '>', '+', '-', '_', ',') as $char)
                $string2 = str_replace($char, '', $string2);
            $string3 = str_replace(' ', '', $string2); //без осн. спецсимв. и пробелов
            $string4 = str_replace(' ', '', $string); //без пробелов
            $words1 = explode(' ', $string); //по словам
            $words2 = explode(' ', $string2); //по словам без спецсимв.

            $db = DB::select('id', 'name');
            $db->from('products')->where('name', 'LIKE', '%' . $string . '%');
            $db->or_where('name', 'LIKE', '%' . $string2 . '%');
            $db->or_where('name', 'LIKE', '%' . $string3 . '%');
            $db->or_where('name', 'LIKE', '%' . $string4 . '%');
            $db->or_where('name', 'LIKE', '%' . implode('%', $words1) . '%');
            $db->or_where('name', 'LIKE', '%' . implode('%', array_reverse($words1)) . '%'); //слова в обратном порядке
            $db->or_where('name', 'LIKE', '%' . implode('%', $words2) . '%');
            $db->or_where('name', 'LIKE', '%' . implode('%', array_reverse($words2)) . '%'); //слова в обратном порядке (без спецсимв.)

            echo json_encode($db->limit(20)->execute()->as_array());
        }
        else
            echo '[]'; //ничего не искали? ничего и не нашли
    }

    /**
     * Смена статуса заказа $id на статус $status
     * @param int $id
     * @param string $status
     */
    public function action_changestatus($id = null, $status = null)
    {
        if (!Auth::instance()->logged_in('admin')) //менять статус может только администратор
            die(Request::factory('error/404')->execute());
        if (!$status || !$id) //все параметры должны быть заданы
            die('No direct script access.');
        $state = ORM::factory('state_order')->where('name', '=', urldecode($status))->find();
        if ($state) //если такой статус найден
        {
            $object = ORM::factory('order', $id); //находим id покупки
            $object->__set('status', $state); //устанавливаем id статуса
            $object->save(); //сохраняем
        }
    }

    /**
     * Выводит дополнительную информацию о заказе
     * @param int $id
     */
    public function action_orderinfo($id = null)
    {
        if (!$id || !Auth::instance()->logged_in('admin')) //имеет доступ только администратор
            die(Request::factory('error/404')->execute());

        $products = ORM::factory('ordproduct')
            ->where('id', '=', $id)
            ->find_all();
        $sumAll = 0;
        $curr = Session::instance()->get('currency');
        if (!$curr)
            $curr = DEFAULT_CURRENCY;
        $currency = Model::factory('Config')->getCurrency($curr);

        echo '<hr><div>Заказ номер ' . ((int)$id) .
            ' <img align="right" style="cursor:pointer;" src="' . url::base() . //картинка, которая чистит содержимое родительского тэга (jQuery need)
            'images/x.png" alt="hide" title="Спрятать" onclick="$(this).parent().parent().html(\'\');"></div><ul>';
        foreach ($products as $p)
        {
            echo '<li>';
            $product = ORM::factory('product', $p->product);
            if (!$p->whs)
                echo '<s>';
            if ($product->name)
            {
                $pr = $product->price * $currency;
                if ($pr < round($pr, 2))
                    $pr = round($pr, 2);
                $sum = $p->count * $pr;
                $sumAll += $sum;
                echo $product->name . ' (' . $p->count . ' ед.) по цене ' . round($pr, 2) . ' ' . $curr;

                if (!$p->whs)
                {
                    echo '</s> Продукт есть в заказе, но нет в наличии!';
                    $sumAll -= $sum;
                }
                echo '</li>';
            }
            else
                echo '(Продукт удален)</s></li>';


        }
        echo '</ul>';
        $user = ORM::factory('order', $id)->__get('user');
        if ($user)
        {
            $pct = Model::factory('Group')->get_pct($user);
            if ($pct != 1)
            {
                $sumAll *= $pct;
                echo 'Скидка ' . round((1 - $pct) * 100, 2) . '%<br>';
            }
        }
        echo 'Итого к оплате: ' . round($sumAll, 2) . ' ' . $curr;
    }

    /**
     * Обновляет настройки
     */
    public function action_config()
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');

        Cache::instance()->delete('menuItem');
        Cache::instance()->delete('apis');
        Cache::instance()->delete('htmlBlocks');

        //установка пунктов меню
        for($menuItem = 1; $menuItem < 9; $menuItem++)
            $menu[$menuItem] = (isset($_POST['menu' . $menuItem])) ? 1 : 0;

        Model::factory('menuItem')->set($menu);

        //установка bool настроек
        $boolConfigs = array(
            'bigCart', 'currency', 'LastNews', 'ordJabb', 'refpp', 'ordMail', 'ShowBlog', 'timeFooter', 'poll',
            'regOrder', 'comments', 'invite'
        );
        $boolConfigsSave = array();
        foreach($boolConfigs as $key)
            $boolConfigsSave[$key] = isset($_POST[$key]) ? 1 : 0;
        unset($boolConfigs);
        Model::factory('config')->setBool($boolConfigsSave);

        // html blocks
        Model::factory('html')->setblock('shopName', isset($_POST['shopName']) ? $_POST['shopName'] : 'Магазин PHP5shop');
        Model::factory('html')->setblock('keywords', isset($_POST['keywords']) ? $_POST['keywords'] : '');

        //настройки сторонних API
        Model_Apis::set('analytics', isset($_POST['analytics']) ? $_POST['analytics'] : '');
        Model_Apis::set('sape', isset($_POST['sape']) ? $_POST['sape'] : '');
        Model_Apis::set('disqus', isset($_POST['disqus']) ? $_POST['disqus'] : '');
        Model_Apis::set('vkcomments', isset($_POST['vkcomments']) ? $_POST['vkcomments'] : '');

        $email = isset($_POST['email']) ? $_POST['email'] : null;
        if (validate::email($email))
        {
            $objE = ORM::factory('mail', 1);
            $objE->__set('value', $email);
            $objE->save();
        }
        else
        {
            $statusError = 1;
            echo ' Email не прошел проверку и не сохранен. ';
        }

        $jabber = isset($_POST['jabber']) ? $_POST['jabber'] : null;
        if (validate::email($jabber))
        {
            $objJ = ORM::factory('mail', 2);
            $objJ->__set('value', $jabber);
            $objJ->save();
        }
        else
        {
            $statusError = 1;
            echo ' Jabber не прошел проверку и не сохранен. ';
        }

        $email3 = isset($_POST['email3']) ? $_POST['email3'] : null;
        if (validate::email($email3))
        {
            $objE = ORM::factory('mail', 3);
            $objE->__set('value', $email3);
            $objE->save();
        }
        else
        {
            $statusError = 1;
            echo ' Email не прошел проверку и не сохранен. ';
        }


        if (isset($statusError))
            echo 'Все остальное успешно сохранено';
        else
            echo 'Успешно сохранено!';
    }

    /**
     * Выполняет добавление(1), обновление(2) и удаление(3) валют
     * @param int $action
     */
    public function action_manageCurr($action = null)
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');
        $error = '';
        switch ($action)
        {
            case 1: //добавление
                if (isset($_POST['code']) && isset($_POST['val']))
                {
                    $code = $_POST['code'];
                    $val = $_POST['val'];
                    if (!is_numeric($val) || !$val)
                        $error = 'Множитель валюты должен быть числом. ';
                    if (!preg_match('/^[A-Z]{3}$/', $code))
                        $error .= 'Код валюты должен состоять из 3 английских букв в верхнем регистре';
                    if ($error)
                    {
                        Session::instance()->set('error_adm', $error);
                        $this->request->redirect(url::base() . 'admin/curr');
                        exit;
                    }
                    Model_Config::addCurrency($code, $val);
                    $this->request->redirect(url::base() . 'admin/curr');
                }
                exit;
            case 2: //обновление
                if (isset($_POST['code']) && isset($_POST['val']))
                {
                    $code = $_POST['code'];
                    $val = $_POST['val'];
                    if (!is_numeric($val) || !$val)
                        $error = 'Множитель валюты должен быть числом. ';
                    if (!preg_match('/^[A-Z]{3}$/', $code))
                        $error .= 'Код валюты должен состоять из 3 английских букв в верхнем регистре';
                    if ($error)
                        die($error);
                    if (Model_Config::setCurrency($code, $val))
                        echo $code . ' (' . $val . ') - Сохранено!';
                    else
                        echo 'Не обновлено! Проверьте введенную информацию';
                }
                exit;
            case 3: //удаление
                if (isset($_POST['code']))
                {
                    $code = $_POST['code'];

                    if ($code == DEFAULT_CURRENCY)
                        die('Эта валюта установлена по умолчанию. Ее нельзя удалять.');

                    Model_Config::delCurrency($code);
                    echo 'ok';
                }
                exit;

            default:
                break;
        }
    }

    /**
     * Выполняет добавление(1), обновление(2) и удаление(3) категорий
     * @param int $action
     */
    public function action_categ($action = null)
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');

        Cache::instance()->delete('cats');

        switch ($action)
        {
            case 1: //добавление
                if (isset($_POST['id']) && isset($_POST['val']))
                {
                    $add = Categories::add($_POST['id'], $_POST['val']);
                    if (count($add))
                        foreach ($add as $field => $error)
                            echo $field . $error . ' ';
                    else
                        echo 'Успешно.';

                }
                Model::factory('sitemap')->update();
                exit;

            case 2: //редактирование
                if (isset($_POST['id']) && isset($_POST['val']) && isset($_POST['parentId']))
                {
                    if (empty($_POST['val']))
                        die('Введите новое название');
                    $cat = new Categories();
                    if ($cat->update($_POST['id'], $_POST['parentId'], $_POST['val']) === FALSE)
                        Session::instance()->set('error_adm', 'Нельзя поместить категорию в дочернюю. ');
                }

                exit;

            case 3: //удаление
                if (isset($_POST['id']))
                {
                    $cat = new Categories();
                    $cats = $cat->getCatChilds($_POST['id']);
                    foreach ($cats as $catId)
                    {
                        $cat->delete($catId);
                        foreach (ORM::factory('product')->where('cat', '=', $catId)->find_all() as $prdct)
                        {
                            $id = $prdct->id;
                            $description = ORM::factory('description', $id);
                            $description->delete();
                            $imagePath1 = $_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $id . '.jpg';
                            $imagePath2 = $_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/small/' . $id . '.jpg';
                            if (file_exists($imagePath1))
                                unlink($imagePath1);
                            if (file_exists($imagePath2))
                                unlink($imagePath2);
                            $n = 1;
                            while (file_exists($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $id . '-' . $n . '.jpg') && $n < 100)
                            {
                                unlink($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $id . '-' . $n . '.jpg');
                                $n++;
                            }
                            ORM::factory('rating_user')->where('product', '=', $id)->delete_all();
                            ORM::factory('rating_value', $id)->delete();
                        }
                        ORM::factory('product')->where('cat', '=', $catId)->delete_all();
                    }
                }
                exit;
            default:
                exit;
        }
    }

    /**
     * Выполняет добавление(1), обновление(2) и удаление(3) групп скидок
     * @param int $action
     */
    public function action_groups($action = null)
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');

        switch ($action)
        {
            case 1: //добавление
                if (isset($_POST['name']) && isset($_POST['pct']))
                {
                    if (empty($_POST['name']))
                    {
                        Session::instance()->set('error_adm', 'Группа должна иметь имя');
                        $this->request->redirect(url::base() . 'admin/groups');
                        exit;
                    }
                    $group = ORM::factory('group');
                    $group->__set('name', $_POST['name']);
                    $pct = str_replace(',', '.', $_POST['pct']);
                    $group->__set('pct', 1 - $pct / 100);
                    $group->save();
                    $this->request->redirect(url::base() . 'admin/groups');
                }
                exit;

            case 2: //редактирование
                if (isset($_POST['name']) && isset($_POST['pct']) && isset($_POST['id']))
                {
                    if (empty($_POST['name']))
                    {
                        echo 'Группа должна иметь имя';
                        exit;
                    }
                    $group = ORM::factory('group', $_POST['id']);
                    $group->__set('name', $_POST['name']);
                    $pct = str_replace(',', '.', $_POST['pct']);
                    $group->__set('pct', 1 - $pct / 100);
                    $group->save();

                    echo 'Успешно сохранено!';
                }
                exit;

            case 3: //удаление
                if (isset($_POST['id']))
                {
                    $group = ORM::factory('group', $_POST['id']);
                    $group->delete();
                    echo 'Удалено';
                }
                exit;
            default:
                exit;
        }
    }

    /**
     * Выполняет добавление(1), обновление(2) и удаление(3) статусов заказов
     * @param int $action
     */
    public function action_status($action = null)
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');

        switch ($action)
        {
            case 1: //добавление
                if (isset($_POST['name']))
                {
                    if (empty($_POST['name']))
                    {
                        $this->request->redirect(url::base() . 'admin/config');
                        exit;
                    }
                    $state = ORM::factory('state_order');
                    $state->__set('name', $_POST['name']);
                    $state->save();
                    $this->request->redirect(url::base() . 'admin/config');
                }
                exit;

            case 2: //редактирование
                if (isset($_POST['name']) && isset($_POST['id']))
                {
                    if (empty($_POST['name']))
                    {
                        echo 'Пустое поле!';
                        exit;
                    }
                    $state = ORM::factory('state_order', $_POST['id']);
                    $state->__set('name', $_POST['name']);
                    $state->save();
                    echo 'Успешно сохранено!';
                }
                exit;

            case 3: //удаление
                if (isset($_POST['id']))
                {
                    $state = ORM::factory('state_order', $_POST['id']);
                    $state->delete();
                    echo 'Удалено';
                }
                exit;
            default:
                exit;
        }
    }

    /**
     * Выполняет обновление и удаление дополнительных пользовательских полей
     */
    public function action_fields()
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST['id'])) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('No direct script access.');

        if (isset($_POST['type']) && isset($_POST['name']))
        {
            $orm = ORM::factory('field', $_POST['id']);
            $orm->__set('type', $_POST['type']);
            $orm->__set('name', $_POST['name']);

            if (isset($_POST['empty']) && stripos($_POST['empty'], 'true') !== FALSE)
                $orm->__set('empty', 1);
            else
                $orm->__set('empty', 0);

            $orm->save();
        }
        else
            Model::factory('field')->del($_POST['id']);
    }

    /**
     * Выполняет добавление(1), обновление(2) и удаление(3) товаров
     * @param int $action
     */
    public function action_products($action = null)
    {
        if (!Auth::instance()->logged_in('admin') || !isset($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');

        Cache::instance()->delete('LastProd');

        $bool = ORM::factory('config')->getBool();
        $rssBlog = $bool['ShowBlog'];

        switch ($action)
        {
            case 1: //добавление

                $file = isset($_POST['filename']) ? //имя локального файла если передано
                    $_POST['filename']
                    : //или
                    APPPATH . 'cache/' . text::random('alnum', 12) . '.xls'; //временный файл

                if (isset($_FILES['importfile']) && !isset($_POST['filename'])) //файл с данными загружен через браузер
                { //во временный файл
                    if (!move_uploaded_file($_FILES['importfile']['tmp_name'], $file))
                        $this->exit2('Ошибка загрузки');
                }

                if (!is_file($file)) //проверка существования файла
                    $this->exit2('Файл не найден');

                $data = new xlsreader; //подключаем специальный модуль
                $data->setOutputEncoding(Kohana::$charset);
                try
                {
                    $data->read($file);
                } catch (Exception $e)
                {
                    if (isset($_FILES['importfile'])) //удаляем временный файл в случае ошибки
                        unlink($file);
                    $this->exit2($e->getMessage());
                }
                if (isset($_FILES['importfile'])) //удаляем временный файл, по ненадобности
                    unlink($file);

                $i = Model_Product::importXls($data->sheets); //счетчик добавленных товаров

                ORM::factory('saveImage')->init();

                if (!$rssBlog)
                    Model::factory('product')->updateFeed('/rss.xml');
                Model::factory('sitemap')->update();
                //сообщение
                $this->exit2(
                    'Обработано: ' . $i . ' за ' . Model_Benchmark::getTimeSec()
                        . ' сек. <br> Загрузка изображений может занять дополнительное время.'
                    , FALSE);

                exit;

            case 2: //редактирование
                if (isset($_POST['name']) && isset($_POST['id']) && isset($_POST['price']) && isset($_POST['whs']))
                {
                    $p = ORM::factory('product', (int)$_POST['id']);
                    if (isset($_POST['cat']))
                        $p->set('cat', (int)$_POST['cat']);
                    $p->set('name', $_POST['name'])
                        ->set('price', (float)$_POST['price'])
                        ->set('whs', (int)$_POST['whs'])
                        ->save();
                    echo 'Успешно сохранено!';
                }
                exit;

            case 3: //удаление
                if (isset($_POST['id']))
                {
                    Model_Product::deleteProduct($_POST['id']);
                    echo 'Удалено';
                }
                exit;
            default:
                exit;
        }
    }

    /**
     * Обработка исключений
     * @param string $text
     * @param bool $die
     */
    private function exit2($text, $die = true)
    {
        if (isset($_FILES['importfile']))
        {
            Session::instance()->set('import', $text);
            $this->request->redirect(url::base() . 'admin/products');
            die();
        }
        else
        {
            if ($die)
                die($text);
            else
                echo $text;
        }
    }

    /**
     * Голосование
     * @param int $id
     */
    public function action_vote($id = 1)
    {
        $user = Auth::instance()->get_user();
        $voted = -1;
        if ($user)
            $voted = Model_Vote::voted($user);

        if ((!Session::instance()->get('voted') && $voted == -1) || !$voted) //пользователь не голосовал
        {
            Session::instance()->set('voted', '1');
            $set = ORM::factory('poll_answer', $id);
            $set->count++;
            $set->save();
        }
        if (!$id && Auth::instance()->logged_in('admin')) //обнуление счетчиков
            foreach (ORM::factory('poll_answer')->find_all() as $answer)
            {
                $answer->count = 0;
                $answer->save();
                Model_Vote::clear_all();
            }
    }

    /**
     * Голосование в рейтинге продукции
     * @param int $value
     */
    public function action_rating($value = 5)
    {
        $user = Auth::instance()->get_user();
        if (!$user || !isset($_SERVER['HTTP_REFERER']))
            exit;
        if (preg_match('#/shop/product([0-9]*)#', $_SERVER['HTTP_REFERER'], $matches))
            $product = $matches[1];
        else
            exit;

        Model_Rating_value::vote($product, $value, $user);
    }

    /**
     * Массовое влияние на цену
     */
    public function action_changePrice()
    {
        if (!(isset($_POST['p']) && isset($_POST['a']) && Auth::instance()->logged_in('admin')))
            die('No direct script access.');

        Model_Product::changePrice(
            str_replace(',', '.', $_POST['p']),
            explode(',', str_replace(' ', '', $_POST['a']))
        );
    }

    public function action_invite()
    {
        if (!Auth::instance()->logged_in('admin') || !count($_POST)) //пользователь не авторизован как admin или зашел на страницу напрямую
            die('Авторизуйтесь');
        if (isset ($_POST['create'])) //создать инвайт и показать
            echo Model_Invite::createInvite((int)$_POST['create']);
        elseif (isset ($_POST['count'])) //показать сколько инвайтов активны
            echo ORM::factory('Invite')->count_all();
        elseif (isset ($_POST['clearall'])) //удалить все
            ORM::factory('Invite')->delete_all();
        elseif (isset ($_POST['clearlast'])) //удалить 1
        {
            if ($_POST['clearlast'] == 1)
                ORM::factory('Invite')->order_by('id', 'DESC')->find()->delete();
            else
                ORM::factory('Invite')->order_by('id')->find()->delete();
        }

    }


    public function action_countOrders()
    {
        die(ORM::factory('order')->count_all());
    }

}