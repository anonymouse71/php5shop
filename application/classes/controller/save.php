<?php defined('SYSPATH') or die('No direct script access.');
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


/**
 * Назначение класса: обойти ограничение хостинга 'max_execution_time'
 */

class Controller_Save extends Controller{

/**
 * Сохраняет на сервере 1 изображение из очереди 'saveImage'
 *  (изменяя размер и добавляя watermark )
 * Удаляет его из очереди. Если очередь не пуста,
 * рекурсивно запускает себя.
 */
    public function action_img()
    {
        ignore_user_abort(TRUE);
        $obj = ORM::factory('saveImage');
        $img = $obj->find();
        if ($img->id && $img->url)
        {
            if ($img->n)
                $obj->gd($img->url, $img->id . '-' . $img->n);
            else
                $obj->gd($img->url, $img->id);

            DB::delete('saveimages')
                ->where('n', '=', $img->n)
                ->and_where('id', '=', $img->id)
                ->execute();
        }

        if (ORM::factory('saveImage')->count_all() > 0)
            $obj->init();
    }
}