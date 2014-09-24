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
class Model_Page extends ORM
{
    protected static $menu_items;
    protected static $cache_key = 'special_pages';

    /**
     * Возвращает асоциативный массив, ключами которого являются uri страниц, а значениями - их названия
     * @return array
     */
    public static function get_menu()
    {

        if (!isset(self::$menu_items))
        {
            self::$menu_items = Cache::instance()->get(self::$cache_key);

            if (!isset(self::$menu_items))
            {
                self::$menu_items = DB::select('name', 'path')->from('pages')
                    ->where('enabled', '=', '1')
                    ->execute()->as_array('path', 'name');
                Cache::instance()->set(self::$cache_key, self::$menu_items);
            }
        }

        return self::$menu_items;
    }

    public function set($column, $value)
    {
        parent::__set($column, $value);
        return $this;
    }

    public function save()
    {
        Cache::instance()->delete(self::$cache_key);
        return parent::save();
    }

    public function delete($id = NULL)
    {
        Cache::instance()->delete(self::$cache_key);
        return parent::delete($id);
    }

}