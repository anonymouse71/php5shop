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

class Controller_Send extends Controller
{
    public function action_emails()
    {                                                                           //рекурсивная рассылка email
        @ignore_user_abort(TRUE);

        if(!ORM::factory('send_email')->count_all())
            exit;
        $email = ORM::factory('send_email')->find();
        $id = $email->id;
        $to = $email->to;

        $text = ORM::factory('send_text',$id);
        $code = $text->text;
        $title = $text->title;
        
        $mail = Model::factory('PHPMailer');
        $name = Model::factory('html')->getblock('shopName');
        $email = ORM::factory('mail',3)->__get('value');
        $mail->AddReplyTo($email,$name);
        $mail->From = $email;
        $mail->FromName = $name;
        $mail->AddAddress($to);
        $mail->Subject  = $title;
        $mail->MsgHTML('<body>' . $code . '</body>');
        $mail->WordWrap = 80;

        if(Model_Benchmark::getTimeSec() < 5)                                   //если скрипт выполнялся меньше 5 секунд
        {                                                                       //можно сделать паузу между отправками
            $max_execution_time = ini_get('max_execution_time');

            if($max_execution_time >= 30 && $max_execution_time < 60)           //пауза с небольшим запасом в зависимости от max_execution_time
                sleep(20);
            else
                sleep(50);
        }

        $mail->Send();

        ORM::factory('send_email',$id)->delete();
        
        if(!ORM::factory('send_email')->where('id','=',$id)->count_all())
            $text->delete();
                                                                                //следующие вызовы происходят от имени "клиента"
        $curl = curl_init('http://' . $_SERVER['HTTP_HOST'] . url::base() . 'send/emails');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1); 
        curl_exec($curl);
        curl_close($curl);
    }

}