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

class Controller_Page extends Controller_Site
{
    public function action_blog()
    {
        $id = $this->request->param('id', 0);

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

            $this->template->about .= new Pagination(
                array(
                     'uri_segment'    => 'page',
                     'total_items'    => Model::factory('BlogPost')->find_all()
                         ->count(),
                     'items_per_page' => $this->blogLimit,
                ));
            $this->template->title .= ' - Новости магазина'; //дополняем заголовок страницы

        }
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

}
