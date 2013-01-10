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
 * Класс для защиты от перебора паролей (Bruteforce атак)
 * Позволяет ограничить количество авторизаций в сутки с одного IP адреса
 *
 * NOTE: С версии 1.5 данный клас не нужен
 */


class Model_Antibrut
{
    public $attempts = 5; //количество попыток неправильного ввода пароля, которое дается каждому пользователю
    public $count; //количество оставшихся попыток неправильного ввода пароля

    protected $date; //текущая дата в формате СУБД MySQL
    protected $ip; //ip в виде int
    protected $insert = TRUE; //INSERT или UPDATE

    public function  __construct()
    {

        $ipDec = explode('.', $_SERVER['REMOTE_ADDR']); //из IP адреса создаем числовой эквивалент
        $this->ip = (string)(256 * 256 * 256 * $ipDec[0] + 256 * 256 * $ipDec[1] + 256 * $ipDec[2] + $ipDec[3]);
        $this->date = (string)date('Y-m-d');
        DB::delete('antibruteforce')->where('date', '<', $this->date)->execute(); //удаление всех устаревших записей
    }

    /**
     * Проверяет имеет ли пользователь право пробовать авторизоваться
     * @return bool
     */
    public function chk()
    {
        $find = DB::select()->from('antibruteforce')
            ->where('ip', '=', $this->ip)
            ->limit(1)
            ->execute()
            ->as_array('ip', 'try');

        if (count($find))
        {
            $this->count = $find[$this->ip];
            $this->insert = FALSE;

            if ($this->count == 0)
            {
                return FALSE;
            }
        }
        else
        {
            $this->count = $this->attempts;
        }

        return TRUE;
    }

    /**
     * Выполняется после chk() и только в том случае, если пользователь ошибся в авторизации
     * Записывает соответсвующую информацию в БД
     */
    public function bad()
    {
        if ($this->insert === FALSE)
        {
            DB::update('antibruteforce')
                ->value('try', ($this->count - 1))
                ->where('ip', '=', $this->ip)
                ->limit(1)
                ->execute();
        }
        else
        {
            DB::insert('antibruteforce')
                ->values(array($this->ip, ($this->attempts - 1), $this->date))
                ->execute();
        }
    }

    /**
     * Разблокирует пользователя
     */
    public function unlock()
    {
        DB::delete('antibruteforce')->where('ip', '=', $this->ip)
            ->limit(1)
            ->execute();
    }
}