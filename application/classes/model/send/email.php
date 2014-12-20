<?php defined('SYSPATH') OR die('No direct access allowed.');
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

class Model_Send_email extends ORM
{
    public $absolute_path; // site URL where php5shop installed

    public function __construct($id = NULL)
    {
        parent::__construct($id);

        $this->absolute_path = 'http://' . $_SERVER['HTTP_HOST'] . url::base();
    }

    /**
     * Creates email newsletter
     * @param string $subject
     * @param string $email_html
     * @param int $time
     */
    public function send($subject, $email_html, $time = 0)
    {
        $text = ORM::factory('send_text');
        $text->title = $subject;
        $text->text = $this->replace_relative_links($email_html);
        $text->save();
        $text->reload();

        $users = DB::select('email')->distinct(TRUE)->from('users')
            ->where('email', '!=', '');

        if ($time < 0)
            $users->and_where('last_login', '<', $time);

        // отправка только тем пользователям, которые согласились получать рассылку
        if (ORM::factory('field')->where('id', '=', 1)->count_all())
            $users->join('field_values', 'left')
                ->on('uid', '=', 'users.id')
                ->on('field', '=', DB::expr('1'))
                ->and_where('value', '=', 'on');

        foreach ($users->execute()->as_array(NULL, 'email') as $email)
        {
            $send = ORM::factory('send_email');
            $send->to = $email;
            $send->id = $text->id;
            $send->save();
        }

        $curl = curl_init($this->absolute_path . 'send/emails');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_exec($curl);
        curl_close($curl);
    }

    /**
     * Replaces relative a[href] and img[src] to absolute
     * @param string $html
     * @return string
     */
    protected function replace_relative_links($html)
    {
        $url = $this->absolute_path;
        if (mb_substr($url, -1, 1, 'UTF-8') == '/')
            $url = mb_substr($url, 0, mb_strlen($url, 'UTF-8') - 1, 'UTF-8');
        $html = preg_replace('/<a([^>]+)href="(\/[^"]+)"/i', '<a$1href="' . $url . '$2"', $html);
        $html = preg_replace('/<img([^>]+)src="(\/[^"]+)"/i', '<img$1src="' . $url . '$2"', $html);
        return $html;
    }
}