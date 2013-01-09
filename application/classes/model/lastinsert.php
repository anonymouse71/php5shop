<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Запрос для получения id последнего добаленного элемента
 */
class Model_LastInsert
{
    /**
     * Возвращает id последнего вставленного элемента
     * @return int
     */
    public static function id()
    {
        $lastInsert = DB::query(Database::SELECT, 'SELECT LAST_INSERT_ID()')->execute()->as_array();
        return $lastInsert[0]['LAST_INSERT_ID()'];
    }

    /**
     * Возвращает id последнего вставленного элемента заданой модели ORM
     * @param string $model
     * @return int
     */
    public static function id_from_orm($model)
    {
        $obj = ORM::factory($model)->order_by('id','desc')->find();
        if(isset($obj->id))
            return $obj->id;
        else
            return 0; 
    }
}