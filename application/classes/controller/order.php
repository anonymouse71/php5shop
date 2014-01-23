<?php defined('SYSPATH') or die('No direct script access.');

/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2013-2014 phpdreamer
 * php5shop.com
 * email: phpdreamer@rambler.ru
 * Это программа является свободным программным обеспечением. Вы можете
 * распространять и/или модифицировать её согласно условиям Стандартной
 * Общественной Лицензии GNU, опубликованной Фондом Свободного Программного
 * Обеспечения, версии 3.
 * Эта программа распространяется в надежде, что она будет полезной, но БЕЗ
 * ВСЯКИХ ГАРАНТИЙ, в том числе подразумеваемых гарантий ТОВАРНОГО СОСТОЯНИЯ ПРИ
 * ПРОДАЖЕ и ГОДНОСТИ ДЛЯ ОПРЕДЕЛЁННОГО ПРИМЕНЕНИЯ. Смотрите Стандартную
 * Общественную Лицензию GNU для получения дополнительной информации.
 * Вы должны были получить копию Стандартной Общественной Лицензии GNU вместе
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/
 */

class Controller_Order extends Controller_Site
{
    public function action_cart()
    {
        $this->template->title .= ' - Покупки';
        $this->template->breadcrumbs[] = array('Корзина', url::base() . 'order/cart');
        $cart = Session::instance()->get('cart');
        if (!is_array($cart))
        {
            $cart = array();
        }
        $cart = array_unique($cart);
        $bcart = Session::instance()->get('bigCart');
        if (!is_array($bcart))
        {
            $bcart = array();
        }
        $this->template->about = new View(TPL . 'shopingCart');
        $i = 0;
        $curr = Model::factory('config')->getCurrency($this->currency); //получение курса валют
        if (!$curr) //если в сессию попал поддельный или уже не существующих в БД курс
        {
            Session::instance()->delete('currency'); //удаляем его из сессии
            $this->request->redirect($_SERVER['REQUEST_URI']); //и обновляем страницу
        } //получаем множитель скидки для пользователя
        $pct = isset($this->user->id) ? Model::factory('group')->get_pct($this->user->id) : 1;
        foreach ($cart as $id)
        {
            $products[$i] = ORM::factory('product', $id)->as_array();
            if (is_array($products[$i]))
            {
                $products[$i]['price'] = round($curr * $products[$i]['price'] * $pct, 2) . ' ' . $this->currency;
                if (isset($bcart[$id]))
                {
                    $products[$i]['count'] = $bcart[$id];
                }
                else
                {
                    $products[$i]['count'] = 1;
                }
                $i++;
            }
        }
        if (isset($products) && is_array($products))
        {
            $this->template->about->products = $products;
        }
        else
        {
            $this->template->about->products = array();
        }

        $this->template->about->sum = Model_Ordproduct::sum();
    }

    public function action_index()
    {
        //получение списка продуктов из корзины
        $products = Session::instance()->get('cart');
        $counts = Session::instance()->get('bigCart');
        if (!is_array($products) || !count($products))
        {   //если продуктов там нет - перенаправление
            $this->request->redirect(url::base());
        }

        $this->template->breadcrumbs[] = array('Корзина', url::base() . 'order/cart');
        $this->template->breadcrumbs[] = array('Оформление заказа', url::base() . 'order');

        $way = null; //способ заказа
        if (isset($_POST['way']) && ORM::factory('pay_type', $_POST['way']))
        {
            Session::instance()->set('way', $_POST['way']);
        }

        if (isset($_POST['unsetway']))
        {
            Session::instance()->delete('way');
        }

        $this->template->about = new View('order');

        if(Auth::instance()->logged_in())
        {
            $this->template->about->register = 1;
            $user = Auth::instance()->get_user()->as_array(); //извлекаем данные о пользователе в массив
        }
        else
            $this->template->about->register = 0;

        $this->template->about->message = FALSE;
        //форма с информацией о пользователе
        $this->template->about->userInfo = View::factory(TPL . 'userInfoForm');
        $this->template->about->userInfo->fields = ORM::factory('field')->find_all()->as_array();
        //наполняем ее информацией
        $fieldNames = array('username', 'email', 'phone', 'address');
        if(isset($_POST['username'], $_POST['email'], $_POST['phone'], $_POST['address']))
        {
            $this->template->about->userInfo->val = $_POST;
        }
        elseif($this->template->about->register)
        {
            $this->template->about->userInfo->val = array();
            foreach($fieldNames as $key)
                $this->template->about->userInfo->val[$key] = $user[$key];

            if (count($this->template->about->userInfo->fields))
            {
                $fieldORM = ORM::factory('field_value');
                foreach ($this->template->about->userInfo->fields as $field)
                    $this->template->about->userInfo->val['f' . $field->id]
                        = $fieldORM->get($field->id, $user['id']);
            }
        }
        else
        {
            $this->template->about->userInfo->val = array();
            foreach($fieldNames as $key)
                $this->template->about->userInfo->val[$key] = '';
            foreach ($this->template->about->userInfo->fields as $field)
                $this->template->about->userInfo->val['f' . $field->id] = '';
        }


        $this->template->about->userInfo->errors = Model::factory('user')->updateUser(
            isset($user['id']) ? $user['id'] : 0, //user_id
            $this->template->about->userInfo->val, //values
            false,                                //is_admin
            true,                                  //show "ok" messages
            !isset($user['id'])                    //readonly mode
        );


        $emails = ORM::factory('mail')->find_all()->as_array('id', 'value'); //получение email и jabber менеджера
        $to = array();
        //найден email и в настройках установлено отправлять на него
        if ($this->boolConfigs['ordMail'] && isset($emails[1]))
            $to['email'] = $emails[1];

        //найден jabber и в настройках установлено отправлять на него
        if ($this->boolConfigs['ordJabb'] && isset($emails[2]))
            $to['jabber'] = $emails[2];



        $stop = $this->boolConfigs['regOrder']; //Нельзя совершать покупки без регистрации?
        $this->template->about->stop = $stop;
        $way = Session::instance()->get('way', null);
        if (ORM::factory('pay_type')->count_all() == 1)
        {
            $way = ORM::factory('pay_type')->find()->__get('id');
        }
        else
        {
            $this->template->about->ways
                = ORM::factory('pay_type')->where('active', '=', 1)->find_all();
        }

        if ($this->template->about->register) //пользователь авторизован?
        {
            if (!$this->template->about->userInfo->errors && $way && isset($_POST['confirm']))
            {   //есть все необходимое для заказа
                // добавляем к информации о заказе id пользователя
                $this->template->about->userInfo->val['id'] = $user['id'];
                //заказ сохранен
                $message = Model_Order::create($products, $this->template->about->userInfo->val, $to, $way, $counts);
            }

        }
        elseif (!$this->template->about->userInfo->errors && !$stop && $way && isset($_POST['confirm']))
        {
            //заказ сохранен
            $message = Model_Order::create($products, $this->template->about->userInfo->val, $to, $way, $counts);
        }

        // напоминание, что нужно исправить контактные данные
        if(!empty($way) && $this->template->about->userInfo->errors )
            $this->template->about->errors = true;
        else
        {
            $this->template->about->errors = false;
            if(empty($way) && mb_strlen($this->template->about->userInfo->errors, 'utf-8') > 35)
                $this->template->about->userInfo->errors = ''; // на 1 этапе не показываем много ошибок.
        }


        if ($way)
        {
            $order_id = Session::instance()->get('order_id', 0);
            $this->template->about->order_id = $order_id;
            if (!$order_id)
            {
                $order_id = $this->template->about->order_id = Model::factory('Tmp_Order')->new_id();
                Session::instance()->set('order_id', $order_id);
            }
            if ($way == 4)
            {
                // оплата через interkassa
                if (isset($message))
                {
                    $shop = Model_Interkassa_Shop::factory(array(
                        'id' => Model_Apis::get('ik_shop_id'),
                        'secret_key' => 'not_set' // we don't need it now
                    ));
                    $currency = DB::select('name')->from('currency')->where('value', '=', 1)->limit(1)
                        ->execute()->as_array(null, 'name');
                    if (count($currency))
                        $currency = $currency[0];
                    else
                        $currency = DEFAULT_CURRENCY;

                    $description = 'Заказ #' . $order_id . ' в магазине ' . $_SERVER['HTTP_HOST'];
                    $payment = $shop->createPayment(array(
                        'id' => $order_id,
                        'amount' => Session::instance()->get('order_sum', 0),
                        'description' => $description,
                        'locale' => 'ru',
                        'currency' => $currency
                    ));
                    $payment->setBaggage($order_id);

                    $this->template->about->ik_payment = $payment;

                    Session::instance()->delete('order_sum');
                }
                else
                    Session::instance()->set('order_sum', number_format(Model_Ordproduct::sum(false), 2, '.', ''));
            }
            $this->template->about->way = ORM::factory('pay_type', $way);
            $text = $this->template->about->way->text;
            $text = str_replace('{{id}}', Model::factory('Tmp_Order')->new_id(), $text); //предварительный id заказа
            $text = str_replace('{{sum}}', Model_Ordproduct::sum(), $text); //сумма заказа
            $this->template->about->way->text = $text;
        }

        if (isset($message)) //статус = ok
        {
            $this->template->about->message = $message;
            Session::instance()->delete('cart'); //корзина очищена
            Session::instance()->delete('bigCart');
            Session::instance()->delete('way');
            $this->template->topBlock2->items = 0; //виджет корзины обновлен
        }
    }
}
