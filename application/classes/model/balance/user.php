<?php defined('SYSPATH') or die('No direct script access.');

class Model_Balance_user extends ORM
{
    public function balance($uid,$format=TRUE)
    {
        $balance_ = ORM::factory('balance_user',$uid)->__get('balance');
        if(!$balance_)
            $balance_ = 0;
        $b = Model::factory('referral')->where('id','=',$uid)->find_all();
        $curr = Session::instance()->get('currency');
        if(!$curr)
            $curr = DEFAULT_CURRENCY;
        $currency = Model::factory('Config')->getCurrency($curr);
        if(!is_object($b))
            return '0 ' . $curr;
        $balance = 0;
        foreach($b as $user)
        {
            $orders = Model::factory('order')->where('user','=',$user->ref)->find_all();
            if(is_object($orders))
                foreach($orders as $order)
                    if($order->status == 0)
                    {
                        $ordp = Model::factory('ordproduct')->where('id','=',$order->id)->find_all();
                        if(is_object($orders))
                            foreach($ordp as $pr)
                            {
                                $price = ORM::factory('product',$pr->product)->__get('price');
                                if($price)
                                    $balance += $price * $pr->count;
                            }
                    }
        }
        if($format)
            return round( ($balance * Model::factory('affiliate')->percent() * 0.01  + $balance_) * $currency, 2) . ' ' . $curr;
        else
            return round( ($balance * Model::factory('affiliate')->percent() * 0.01  + $balance_) , 2);
    }

    public function balance_set($uid,$set)
    {
        $b = ORM::factory('balance_user',$uid);
        if(!$b->id)
        {
            $b = ORM::factory('balance_user');
            $b->id = $uid;
            $b->balance = 0;
        }
        $balance = $this->balance($uid,FALSE);        
        $b->balance = $set - $balance + $b->balance;

        $b->save();
    }
}
