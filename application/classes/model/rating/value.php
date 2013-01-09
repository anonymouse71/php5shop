<?php defined('SYSPATH') or die('No direct script access.');

class Model_Rating_value extends ORM
{
    /**
     * Выполняет голосование в рейтинге товаров
     * @param int $product
     * @param int $value
     * @param int $user_id
     */
    public static function vote($product, $value, $user_id)
    {
        if($value<1 && $value>5)
            $value = 5;

        if(Model_Rating_user::can_vote($user_id, $product))
        {
            $user = ORM::factory('Rating_user');
            $user->id = $user_id;
            $user->product = $product;
            $user->val = $value;

        }
        else
        {
            $user = ORM::factory('Rating_user')
                    ->where('id','=',$user_id)
                    ->and_where('product','=',$product)->find();
            $old_val = $user->val;
            $user->val = $value;
        }
        $user->save();

        $obj = ORM::factory('Rating_value',$product);
        $val = $obj->val;
        if(!$val)
        {
            $obj = ORM::factory('Rating_value');
            $obj->__set('id', $product);
            $obj->__set('val', (int) $value);
            $obj->__set('count', 1);
        }
        else
        {
            isset($old_val)? null : ++$obj->count;
            $val = round( ($val * $obj->count + $value - ( isset($old_val)? $old_val : 0) ) / $obj->count );
            $obj->__set('val', $val);
        }
        $obj->save();
        
        
    }
}