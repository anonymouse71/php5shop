<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2014-2016 phpdreamer
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

class Model_Cbr
{
    protected $url = 'http://www.cbr.ru/scripts/XML_daily.asp';

    public function update_local_currencies()
    {
        $local_curr = DB::select('name', 'value')->from('currency')->execute()->as_array('name', 'value');
        $actual_curr = $this->get_currencies_from_cbr();
        if (!count($local_curr) || !count($actual_curr))
            return false;
        $actual_curr['RUB'] = 1;
        $curr_val1_name = null;
        foreach ($local_curr as $name => $curr)
            if ($curr == 1.)
            {
                $curr_val1_name = $name;
                break;
            }
        if ($curr_val1_name == null || !isset($actual_curr[$curr_val1_name]))
            return false;

        $db = Database::instance();

        $rows = array();
        foreach ($local_curr as $name => $curr)
            if ($name != $curr_val1_name && isset($actual_curr[$name]))
                $rows[] = '(' . $db->escape($name) . ','
                    . str_replace(',', '.', (string)($actual_curr[$curr_val1_name] / $actual_curr[$name])) . ')';

        if (!count($rows))
            return false;

        $rows = join(',', $rows);
        $table = $db->quote_table('currency');

        // multiple updating rows
        $sql = "INSERT INTO $table (`name`,`value`) VALUES $rows ON DUPLICATE KEY UPDATE `value`=VALUES(value);";

        return DB::query(Database::UPDATE, $sql)->execute();
    }

    public function get_currencies_from_cbr()
    {
        $currency = array();
        try
        {
            $xml = new SimpleXMLElement($this->url, NULL, TRUE);
        } catch (Exception $e)
        {
            Kohana_Log::instance()->add(Kohana::ERROR, $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getMessage());
            return $currency;
        }

        if (!isset($xml->Valute))
            return $currency;

        foreach ($xml->Valute as $curr)
            $currency[(string)$curr->CharCode] = str_replace(',', '.', (string)$curr->Value);

        return $currency;
    }
}