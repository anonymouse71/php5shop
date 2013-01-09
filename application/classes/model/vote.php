<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Информация о том что пользователь уже голосовал
 */
class Model_Vote extends ORM
{
/**
 * Если пользователь не голосовал, записывается что голосовал.
 * Возвращает (bool) голосовал или нет
 * @param int $user
 * @return bool
 */
    public static function voted($user)
    {
        if(!self::is_voted($user))
        {
            $orm = ORM::factory('vote');
            $orm->__set('id', $user);
            $orm->save();
            return FALSE;
        }
        else
            return TRUE;
    }
/**
 * Проверка, голосовал ли пользователь
 * @param int $user
 * @return bool
 */
    public static function is_voted($user)
    {
        if(ORM::factory('vote',$user)->__get('id'))
            return TRUE;
        else
            return FALSE;
    }
/**
 * Очищает данные о голосовавших пользователях
 */
    public static function clear_all() //вызывается в application/classes/controller/ajax.php
    {
        foreach(ORM::factory('vote')->find_all() as $user)
            $user->delete();
    }
}