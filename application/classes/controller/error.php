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
 
class Controller_Error extends Controller {
 
    public function action_404()                                                //ошибка 404
    {
        $this->request->status = 404;
        $this->request->headers['HTTP/1.1'] = '404';
        $this->request->headers['Content-Type']='text/html; charset=UTF-8';
        $this->request->response = new View('404');
    }
 
    public function action_403()                                                //ошибка 403
    {
        $this->request->status = 403;
        $this->request->headers['HTTP/1.1'] = '403';
        $this->request->headers['content-type']='text/html;charset=utf8';
        $this->request->response = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>Ошибка 403</body></html>';
    }
 
    public function action_500()                                                //ошибка 500
    {
        $this->request->status = 500;
        $this->request->headers['HTTP/1.1'] = '500';
        $this->request->headers['content-type']='text/html;charset=utf8';
        $this->request->response = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>Ошибка 500</body></html>';
    }

    public function action_loginlimit()                                         //исчерпаны неудачные попытки авторизации
    {       
        $this->request->response = new View('loginlimit');
    }

    public function action_xsrf()                                               //заблокирована попытка xsrf атаки
    {
        $this->request->response = new View('xsrf');
    }

    public function action_version()                                            //информация о разработчике и версии (страница спрятана)
    {
        $text = base64_decode('PGRpdiBpZD0ibW9kYWwtY29udGVudCI+PGgyPnBocDVzaG9wPC9oMj48YnI+PHA+0JTQstC40LbQvtC6INC40L3RgtC10YDQvdC10YIg0LzQsNCz0LDQt9C40L3QsCDRgSDQvtGC0LrRgNGL0YLRi9C8INC40YHRhdC+0LTQvdGL0Lwg0LrQvtC00L7QvC48L3A+PGJyPjxwPtCh0LDQudGCOiA8YSBocmVmPSJodHRwOi8vcGhwNXNob3AuY29tLyI+aHR0cDovL3BocDVzaG9wLmNvbS88L2E+PC9wPjxwPtCQ0LLRgtC+0YA6IDxhIGhyZWY9Imh0dHA6Ly9waHBkcmVhbWVyLnJ1LyI+cGhwZHJlYW1lcjwvYT48L3A+PGJyPtCS0LXRgNGB0LjRjzog') . VERSION . '</div><script type="text/javascript">$("#modal-content").modal();</script></body>';
        echo function_exists('str_ireplace')? str_ireplace('</body>', $text, Request::factory(url::base())->execute()) : str_replace('</body>', $text, Request::factory(url::base())->execute());
    }

    public function action_off()                                                //сайт временно отключен
    {
        $this->request->response = new View('off');
    }
    
} // End Error
 

