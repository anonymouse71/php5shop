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

class Model_Tmp_Order extends ORM
{
    /**
     * Получает id следующего потенциального заказа
     * @return int
     */
    public function get_id()
    {
        $OrderId = Model_LastInsert::id_from_orm('order');
        $tmpId = Model_LastInsert::id_from_orm('tmp_order');
        if($tmpId > $OrderId)
            return $tmpId + 1;
        else
            return $OrderId + 1;
    }
    /**
     * Резервирует в БД номер заказа
     * @return int
     */
    public function new_id()
    {
        $sid = Session::instance()->get('sid', session_id());
        Session::instance()->set('sid', $sid);

        $isset = ORM::factory('tmp_order')->where('session', '=', $sid)
            ->order_by('id', 'desc')
            ->find_all()->as_array(null, 'id');
        if (isset($isset[0]))
            return $isset[0];
        
        $tmp_order = ORM::factory('tmp_order');
        $tmp_order->id = $this->get_id();
        $tmp_order->session = $sid;
        $tmp_order->time = time();
        $tmp_order->save();

        foreach(ORM::factory('tmp_order')->where('time','<', (time() - 60*60*24*2))->find_all() as $tmp)
            $tmp->delete(); //заказы не подтвержденные за 48 часов - удаляются
        
        return $tmp_order->id;
    }

    public function clean()
    {
        if(Session::instance()->get('sid'))
            foreach(
                ORM::factory('tmp_order')
                    ->where('session','=',Session::instance()->get('sid'))
                    ->find_all()
                as $t
                )
                    $t->delete();
        
    }
}