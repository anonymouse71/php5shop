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

class Controller_Interkassa extends Controller_Site
{
    public function action_success()
    {
        if (!Model_Meta::special_meta_tags())
            $this->template->title .= ' - Платеж принят успешно.';
        $this->template->about = 'Спасибо за покупку. Платеж принят успешно, ожидайте выполнения заказа.';
    }

    public function action_fail()
    {
        if (!Model_Meta::special_meta_tags())
            $this->template->title .= ' - Ошибка при получении платежа!';
        $this->template->about = 'Произошла ошибка при выполнении платежа.'
            . ' Если Вы перевели деньги, обратитесь к администрации для устранения проблемы.';

    }

    public function action_status($hash)
    {
        $this->template = null;
        $this->request->response = '';

        if (self::getStatusPageId() != $hash)
        {
            $this->request->status = 404;
            return;
        }

        $shop = Model_Interkassa_Shop::factory(array(
            'id' => Model_Apis::get('ik_shop_id'),
            'secret_key' => Model_Apis::get('ik_secret_key')
        ));

        try
        {
            $status = $shop->receiveStatus($_POST);
        } catch (Model_Interkassa_Exception $e)
        {
            // The signature was incorrect, send a 400 error to interkassa
            // They should resend payment status request until they receive a 200 status
            $this->request->status = 400;
            Kohana_Log::instance()->add('Interkassa Error', $e->getMessage());
            return;
        }

        $payment = $status->getPayment();
        $order = ORM::factory('order')->find($payment->getBaggage());
        if (!$order->id)
        {
            $this->request->status = 400;
            Kohana_Log::instance()->add('Interkassa Error', 'Order not found');
            return;
        }
        $order->paid += $payment->getAmount();
        $order->save();
        $this->request->status = 200;
    }

    public static function getStatusPageId()
    {
        $str = DB::select(DB::expr('md5(CONCAT_WS(identity, network, profile, username, email, id)) AS i'))
            ->from('users')
            ->join('roles_users')->on('users.id', '=', 'roles_users.user_id')
            ->where('role_id', '=', 2)
            ->order_by('users.id', 'ASC')
            ->limit(1)
            ->execute()->as_array(null, 'i');
        return $str[0];
    }
}
