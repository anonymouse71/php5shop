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
    public function send($title,$code,$time=NULL)
    {
        $text = ORM::factory('send_text');
        $text->__set('title', $title);
        $text->__set('text', $code);
        $text->save();
        $id = Model_LastInsert::id();

        if($time)
            $users = ORM::factory('user')->where('last_login','<',$time)->find_all();
        else
            $users = ORM::factory('user')->find_all();

        

        foreach ($users as $user)
        {            
            $send = ORM::factory('send_email');
            $send->to = $user->email;
            $send->id = $id;
            $send->save();            
        }
        
        $curl = curl_init('http://' . $_SERVER['HTTP_HOST'] . url::base() . 'send/emails');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_exec($curl);
        curl_close($curl);
    }
}