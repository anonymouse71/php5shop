<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Сиситема инвайтов
 */
class Model_Invite extends ORM {
    /**
     * Создать инвайт в группу $groupId
     * @param int $groupId 
     */
    public static function createInvite($groupId=0)
    {
        do                                                                      //создаем уникальный код инвайта
        {
            $code = Kohana_Text::random('alpha', 10);
        }
        while(ORM::factory('invite')->where('code','=',$code)->__get('id'));

        $orm = ORM::factory('invite');                                          //создаем ORM объект
        $orm->__set('code', $code);                                             //устанавливаем значения
        $orm->__set('group', $groupId);
        $orm->save();                                                           //сохраняем в базу
        
        return $code;                                                           //возвращаем код инвайта
    }
    
    /**
     * Если код инвайта правильный,
     * возвращает id группы в которую занести пользователя
     * иначе возвращает -1
     * @param string $code
     * @return int
     */
    public static function checkInvite($code)
    {
        $db = ORM::factory('invite')->where('code','=',$code)                
                ->find()
                ->as_array();
        return isset($db['group']) ? (int) $db['group'] : -1;                   //инвайт найден? вернуть группу, нет? вернуть "-1"
    }

    /**
     * Удаляет использованный инвайт
     * @param string $code 
     */
    public static function deleteInvite($code)
    {
        return ORM::factory('invite')->where('code','=',$code)->delete_all();
    }
    
}