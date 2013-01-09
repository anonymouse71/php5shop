<?php defined('SYSPATH') or die('No direct script access.');

class Model_Rating_user extends ORM
{
    /**
     * Определяет не голосовал ли уже пользователь за данный продукт в рейтинге
     * Если не голосовал, возвращает TRUE
     * @param int $user_id
     * @param int $product
     * @return bool
     */
    public static function can_vote($user_id,$product)
    {
        return !(bool)(
                ORM::factory('Rating_user')
                ->where('id','=',$user_id)
                ->and_where('product','=',$product)
                ->count_all()
                );  
    }
}