<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Descr_cat - описание категории
 */
class Model_Descr_cat extends ORM {

/**
 * Возвращает HTML код описания категории
 * @param int $id
 * @return string
 */
    public static function get($id)
    {
        $str = ORM::factory('Descr_cat', $id)->__get('text');
        return $str ? $str : '';
    }

/**
 * Устанавливает HTML код описания категории
 * @param int $id
 * @param string $text
 * @return bool
 */
    public static function set($id, $text)
    {
        $obj = ORM::factory('Descr_cat', $id);
        if(!$obj->id)
        {
            $obj = ORM::factory('Descr_cat');
            $obj->id = $id;
        }
        $obj->text = $text;
        return $obj->save();
    }
}