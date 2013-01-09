<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Ordproduct extends ORM{
    
    /**
     * Метод возвращает сумму заказа пользователя
     */
    public static function sum()
    {
        $sumAll = 0;
        $products = Session::instance()->get('cart');                           //получение списка продуктов из корзины
        $counts = Session::instance()->get('bigCart');
        if(!is_array($products) || !count($products))
            return $sumAll;
        $curr = Session::instance()->get('currency');
        if(!$curr)
            $curr = DEFAULT_CURRENCY;
        $currency = Model::factory('Config')->getCurrency($curr);

        foreach ($products as $p)
        {
            $product = ORM::factory('product',$p);

            if($product->whs)                                                   //считаем только то что в наличии
            {
                $price = $product->price * $currency;
                if($price < round($price,2))                                    //округляем если это выгодно
                    $price = round($price,2);
                if(isset($counts[$p]))
                    $price *= $counts[$p];
                $sumAll += $price;
            }               
        }
        $user = Auth::instance()->get_user();

        if($user)                                                               //скидка
             $sumAll *= Model::factory('Group')->get_pct($user);              

        return round($sumAll,2) . ' ' . $curr;
    }
}