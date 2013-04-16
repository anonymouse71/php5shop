<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Класс дополнительных полей
 */

class Model_Field extends ORM
{
    /**
     * Проводит валидацию дополнительного поля
     * @param string $value
     * @param int $typeId
     * @return bool
     */
    public function validate($value, $typeId=null)
    {
        if(!$typeId)
            $typeId = $this->type;

        $pattern = ORM::factory('field_type', $typeId)->__get('reg');

        if(!$pattern)
            return FALSE;
        else
            return preg_match($pattern, $value);
    }

    public function del($id)
    {
        ORM::factory('field',$id)->delete();
        DB::delete('field_values')->where('id','=',$id)->execute();
    }
}