<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Ordproduct extends ORM
{

    /**
     * Метод возвращает сумму заказа пользователя
     */
    public static function sum($use_currency = true)
    {
        $sumAll = 0;
        $products = Session::instance()->get('cart'); //получение списка продуктов из корзины
        $counts = Session::instance()->get('bigCart');
        if (!is_array($products) || !count($products))
            return $sumAll;
        if ($use_currency)
        {
            $curr = Session::instance()->get('currency');
            if (!$curr)
                $curr = DEFAULT_CURRENCY;
            $currency = Model::factory('Config')->getCurrency($curr);
        }
        else
            $currency = 1;

        foreach ($products as $p)
        {
            $product = ORM::factory('product', $p);

            if ($product->whs) //считаем только то что в наличии
            {
                $price = $product->price * $currency;
                if ($price < round($price, 2)) //округляем если это выгодно
                    $price = round($price, 2);
                if (isset($counts[$p]))
                    $price *= $counts[$p];
                $sumAll += $price;
            }
        }
        $user = Auth::instance()->get_user();

        if ($user) //скидка
            $sumAll *= Model::factory('Group')->get_pct($user);

        $sumAll = round($sumAll, 2);
        return $use_currency ? ($sumAll . ' ' . $curr) : $sumAll;
    }


}