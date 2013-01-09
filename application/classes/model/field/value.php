<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Field_value extends ORM
{
    /**
     * Возвращает значение поля по id поля и id польователя
     * @param int(2) $field
     * @param int $user
     * @return bool
     */
    public function get($field,$user)
    {
        return ORM::factory('field_value',$field)
                ->where('uid','=',$user)
				->and_where('field','=',$field)
				->find()
				->__get('value');
    }

    /**
     * Устанавливает значение поля по id поля и id польователя
     * @param int(2) $field
     * @param int $user
     * @param string(400) $value
     * @return bool
     */
    public function set($field,$user,$value)
    {

        $obj = ORM::factory('field_value')
			->where('uid','=',$user)
			->and_where('field','=',$field)->find();

        if(!$obj->__get('id'))
        {
            $obj = ORM::factory('field_value');
            $obj->__set('uid',$user);
            $obj->__set('field',$field);
        }
        $obj->__set('value',$value);
        return $obj->save();
    }
}