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
 * Класс получения и редактирования конфигурации
 */

class Model_Config
{

    /**
     * Возвращает массив с содержимым таблицы 'configBool'
     * @return array
     */
    public function getBool()
    {
        return DB::select()->from('configBool')
            ->execute()
            ->as_array('name', 'value');
    }

    /**
     * Устанавливает значение $value параметра $name таблицы 'configBool'
     * @param string $name
     * @param bool $value
     * @return void
     */
    public function setBool($name, $value)
    {
        if (is_array($name) && $value == null)
        {
            $query = 'UPDATE ' . Kohana::config('database')->default['table_prefix'] . 'configBool SET value = CASE ';
            foreach ($name as $key => $value)
                $query .= 'WHEN name=' . Database::instance()->escape($key)
                    . ' THEN ' . ($value ? '1' : '0') . ' ';

            $query .= 'END;';

            DB::query(Database::UPDATE, $query)->execute();

        }
        else
            DB::update('configBool')->value('value', ($value ? 1 : 0))
                ->where('name', '=', $name)
                ->limit(1)
                ->execute();
    }

    /**
     * Возвращает курс валюты $code.
     * @param string $code - банковский код валюты (например USD)
     * @return float       - курс
     * @return array       - массив всех валют, если $code не указан
     * @return bool        - FALSE если код не найден
     */
    public static function getCurrency($code = null)
    {
        if (!$code)
            return DB::select()->from('currency')
                ->execute()
                ->as_array('name', 'value');
        else
        {
            $array = DB::select()->from('currency')
                ->where('name', '=', $code)
                ->limit(1)
                ->execute()
                ->as_array('name', 'value');

            return isset($array[$code]) ? (float)$array[$code] : FALSE;
        }

    }

    /**
     * Устанавливает курс валют
     * @param string $name - банковский код валюты (например UAH)
     * @param float $value - значение курса
     * @return bool
     */
    public static function setCurrency($name, $value)
    {
        return DB::update('currency')
            ->value('value', (float)$value)
            ->where('name', '=', $name)
            ->limit(1)
            ->execute();
    }

    /**
     * Добавляет курс валют
     * @param string $name - банковский код валюты (например UAH)
     * @param float $value - значение курса
     * @return bool
     */
    public static function addCurrency($name, $value)
    {
        return DB::insert('currency')
            ->values(array($name, $value))
            ->execute();
    }

    /**
     * Удаляет курс валют
     * @param string $name - банковский код валюты (например UAH)
     * @return bool
     */
    public static function delCurrency($name)
    {
        return DB::delete('currency')
            ->where('name', '=', $name)
            ->limit(1)
            ->execute();
    }

    /**
     * Находит название курса валют со значением 1
     * @return string
     */
    public static function get1Currency()
    {
        $array = DB::select('name')->from('currency')
            ->where('value', '=', 1)
            ->limit(1)
            ->execute()
            ->as_array();
        if (isset($array[0]['name']))
            return ($array[0]['name']);
        else
            return '';
    }
}