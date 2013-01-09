<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * php5shop - CMS интернет-магазина
 * Copyright (C) 2010-2012 phpdreamer
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
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/.
 */
 
class Model_Order extends ORM
{
/**
 * Создает заказ и оповещает менеджера
 * @param array $products - массив с id продуктов в заказе
 * @param array $client   - массив с данными о клиенте
 * @param array $to       - массив с email и\или jabber менеджера
 * @param int   $way      - id способа оплаты
 * @param array $counts   - в массиве ключи - id продуктов, а значения - их количество в заказе
 * @return string $message2user
 */
    public static function create($products,$client,$to,$way,$counts=null)
    {
        $message2user = 'Спасибо! Менеджер уведомлен о заказе.';
        
        $uid = isset($client['id'])? $client['id'] : 0;
        $phone = $client['phone'];

        $id = Model::factory('Tmp_Order')->new_id();
        DB::insert('orders', array('id','user','phone','status','date'))
                ->values(array($id,$uid,$phone,1,time()))
                ->execute();
        
        Model::factory('Tmp_Order')->clean();
        
        $noWhs = array();//массив товаров, у которых недостача на складе
        
        $prodArray = ORM::factory('product')
                ->where('id', 'IN', $products)->find_all()->as_array('id');
        
        foreach($products as $product)
        {
            $whs = $prodArray[$product]->whs;
            $count = isset($counts[$product])? $counts[$product] : 1;
            
            if($whs >= $count)                                                  //хватает наличия
            {
                DB::insert('ordproducts', array('id','product','count','whs'))
                    ->values(array(
                        $id, //id заказа
                        $product,//id товара
                        $count,//кол-во в заказе
                        ($whs >= $count) //достаточно в наличии
                        ))->execute();
                
                $count = $whs - $count;                                         //сколько станет доступно на складе
            }
            else 
            {           
                if($whs > 0)                                                    //Если хоть что-то есть
                    DB::insert('ordproducts', array('id','product','count','whs'))
                        ->values(array(
                            $id, //id заказа
                            $product,//id товара
                            $whs,//покупаем сколько есть
                             1
                            ))->execute();
                DB::insert('ordproducts', array('id','product','count','whs'))
                    ->values(array(
                        $id, //id заказа
                        $product,//id товара
                        $count - ($whs > 0 ? $whs : 0 ), //сколько не хватает
                        0
                        ))->execute();
                
                $count = 0;                                                     //сколько станет доступно на складе
                $noWhs[] = $product;
            }
            
            
            // Уменьшаем наличие на сладе            
            DB::update('products')
                ->set(array('whs' => $count))
                ->where('id', '=', $product)
                ->limit(1)
                ->execute();
        }
        
        Cache::instance()->delete('LastProd');//в кэше были кол-ва whs, которые потеряли актуальность - удаляем этот кэш

        $message = 'В магазине на '. $_SERVER['HTTP_HOST'] .
                   ' поступил новый заказ (id' .
                   $id . ') от пользователя с номером телефона ' .
                   $phone . ".\r\n";
        if($uid)
            $message .= 'Клиент зарегистрирован с id ' . $uid . ".\r\n";
        $message .= 'Заказано ' . count($products) . ' товаров.';
        
        if(count($noWhs))
        {
            $message .= 'В заказе есть товары, которые уже закончились в наличии (' 
                . count($noWhs) . ').';
            $message2user .= '<br><br>Внимание! В заказе есть товары, закончились в наличии на момент заказа:<br><ul>';
            foreach($noWhs as $product)
                $message2user .= '<li>' . htmlspecialchars ($prodArray[$product]->name)
                        . (
                            ($prodArray[$product]->whs > 0)
                            ?
                            (' - есть только ' . $prodArray[$product]->whs . ' ед.')
                            :
                            ' - нет в наличии'
                          )
                        . '</li>';
            $message2user .= '</ul>';
        }
        $message .= "\r\n\r\n Способ оплаты: " . ORM::factory('pay_type',$way)->__get('name');

        if(isset($to['jabber']))
        {
            $conn = new XMPPHP_XMPP('jabber.ru', 5222, 'php5shop@jabber.ru', 'password', 'xmpphp');
            try
            {
                $conn->connect();
                $conn->processUntil('session_start');
                $conn->presence();
                $conn->message($to['jabber'], $message);
                $conn->disconnect();
            } 
            catch(Exception $e)
            {
                //$e->getMessage();
            }
        }

        if(isset($to['email']))
        {
            $mail = Model::factory('PHPMailer');
            $mail->AddReplyTo($to['email'],'no reply');
            $mail->From = $to['email'];
            $mail->FromName = 'Магазин на ' . $_SERVER['HTTP_HOST'];
            $mail->AddAddress($to['email']);
            $mail->Subject  = 'Новый заказ (id' .$id . ')';
            $mail->AltBody = 'Заказ на ' . count($products) . ' товаров';
            $mail->MsgHTML('<body>' . $message . '</body>');
            $mail->WordWrap = 80;
            $mail->Send();
        }
       
        
        return $message2user;
    }
}