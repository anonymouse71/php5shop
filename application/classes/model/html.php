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
 * Модель для получения HTML блоков из БД и установку содержимого этих блоков
 */

class Model_Html{

    /**
     * Получает html коды основных блоков сайта
     * @return array
     */
    public static function getblocks()
    {
        return DB::select()
                ->from('html')
                ->execute()
                ->as_array('name','code');
    }

    /**
     * Получает код блока $name
     * @param string $name
     * @return string
     */
    public static function getblock($name)
    {
        $email = DB::select('code')
                ->from('html')
                ->where('name', '=', $name)
                ->limit(1)
                ->execute();
        if(isset($email[0]['code']))
            return $email[0]['code'];
    }

    /**
     * Устанавливает значение любому HTML блоку сайта
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function setblock($name,$value)
    {
        return DB::update('html')
                ->value('code', $value)
                ->where('name', '=', $name)
                ->limit(1)
                ->execute();
    }

    /**
     * Получает HTML код дополнительной страницы по id
     * (Например, id 1 - страница clients,  id 2 - contacts)
     * @param int $id
     * @return string
     */
    public static function getHtml($id)
    {
        $array = DB::select('html')
                ->from('htmlpgs')
                ->where('id', '=', $id)
                ->limit(1)
                ->execute()
                ->as_array();
        if(!is_array($array))
            return '';
        return $array[0]['html'];
    }

    /**
     * Устанавливает HTML код дополнительной страницы по id
     * (Например, id 1 - страница clients,  id 2 - contacts)
     * @param int $id
     * @param string $value
     * @return bool
     */
    public static function setHtml($id, $value)
    {
        return DB::update('htmlpgs')
                ->value('html', $value)
                ->where('id', '=', $id)
                ->limit(1)
                ->execute();
    }
}