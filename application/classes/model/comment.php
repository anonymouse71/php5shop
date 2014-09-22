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

/*
 * Модель для работы с комментариями к товарам и новостям
 */

class Model_Comment extends ORM
{

    /*
     * Поля таблицы:
        `id` - первичный ключ
        `object` - id товара или новости
        `user` - id пользователя
        `text` - текст комментария
        `rate` - рейтинг, который поставил пользователь (0-5)
        `is_product` - Если 1, то в `object` id товара, если 0 - то id новости
        `username` - Имя которым подписался пользователь
     */


    private static function save_comment($id, $is_product = 1)
    {
        if (isset($_POST['yourName']) && isset($_POST['comText']))
        {
            if (!Auth::instance()->logged_in())
            {
                return 'Вы не авторизованы!';
            }
            $user = Auth::instance()->get_user();

            if (mb_strlen($_POST['comText'], 'utf-8') > 2000)
                return 'Слишком длинное сообщение!';
            if (mb_strlen($_POST['comText'], 'utf-8') < 3)
                return 'Слишком короткое сообщение!';

            if (isset($_POST['captcha']) && Captcha::valid($_POST['captcha'])) //если проверочное изображение введено верно,
            {
                $obj = ORM::factory('comment');
                $obj->__set('object', $id);
                $obj->__set('user', $user->__get('id'));
                $obj->__set('text', $_POST['comText']);
                $obj->__set('rate', $_POST['rate']);
                $obj->__set('is_product', $is_product);
                $obj->__set('username', $_POST['yourName'] ? $_POST['yourName'] : 'Аноним');
                $obj->save();
                header('Location: ' . $_SERVER['REQUEST_URI']);
                die('<html><script>document.location.href += "";</script></html>');
            }
            return 'Проверочное изображение введено неверно';

        }
    }

    public static function form($id, $is_product = 1)
    {
        $form = new View('commentForm');
        $form->captcha = Captcha::instance();
        $form->errors = self::save_comment($id, $is_product);
        $form->auth = Auth::instance()->logged_in();

        return
            self::last10($id, $is_product)
            . $form->render();
    }

    public static function last10($id, $is_product = 1)
    {
        $page = isset($_GET['comments-page']) ? abs((int)$_GET['comments-page']) : 1; //номер страницы
        if (!$page) // > 0
            $page++;

        $data = ORM::factory('comment')
            ->where('object', '=', $id)
            ->and_where('is_product', '=', $is_product)
            ->limit(10)
            ->offset(($page - 1) * 10)
            ->find_all()->as_array();

        $count_all = ORM::factory('comment')
            ->where('object', '=', $id)
            ->and_where('is_product', '=', $is_product)
            ->count_all();

        if (!count($data))
            return '';

        $comments = new View('comments');
        $comments->data = $data;
        $comments->admin = Auth::instance()->logged_in('admin');
        $comments->rate = $is_product;

        $Pagination = new Pagination(array( //создаем навигацию
            'uri_segment' => 'comments-page',
            'total_items' => $count_all,
            'items_per_page' => 10,
            'current_page' => array('source' => 'query_string', 'key' => 'comments-page'),
        ));

        return $comments->render() . $Pagination->render();
    }

}