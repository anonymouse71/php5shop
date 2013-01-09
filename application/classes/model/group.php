<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Group extends ORM {

/**
 * Находит множитель скидки для пользователя с заданым id
 * @param int $id
 * @return float
 */
    public function get_pct($id)
    {                                                                           //проверяем состоит ли пользователь в группах
        $groups_user = ORM::factory('groups_user', $id)->__get('gid');
        if($groups_user)                                                        //если группа найдена,
            return $this->find($groups_user)->__get('pct');                     //возвращаем множитель
        
        return 1;                                                               //по умолчанию множитель 1
    }    
}