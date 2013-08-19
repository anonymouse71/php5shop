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
        $this->template->title .= ' - Платеж принят успешно.';
        $this->template->about = 'Спасибо за покупку. Платеж принят успешно, ожидайте выполнения заказа.';
    }

    public function action_fail()
    {
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

        if (!isset($_POST['ik_baggage_fields'], $_POST['ik_sign_hash'], $_POST['ik_payment_amount'])
            || !isset($_POST['ik_payment_state']) || $_POST['ik_payment_state'] != 'success')
        {
            $this->request->status = 503;
            return;
        }

        $params = array();
        foreach (array('ik_shop_id', 'ik_payment_amount', 'ik_payment_id',
                     'ik_paysystem_alias', 'ik_baggage_fields',
                     'ik_payment_state', 'ik_trans_id', 'ik_currency_exch',
                     'ik_fees_payer') as $key)
            $params[] = isset($_POST[$key]) ? $_POST[$key] : '';
        $params[] = Model_Apis::get('ik_secret_key');
        if ($_POST['ik_sign_hash'] != md5(strtoupper(join(':', $params))))
        {
            $this->request->status = 400;
            return;
        }

        $order = ORM::factory('order')->find($_POST['ik_baggage_fields']);
        if (!$order->id)
        {
            $this->request->status = 503;
            return;
        }
        $order->paid += $_POST['ik_payment_amount'];
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
