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

/*
 * Модель для создания блока со статистической информацией о работе скрипта
 */

class Model_Benchmark
{

    /**
     * Возвращает строку с информацией о времени загрузки страниц
     * @return string
     */
    public static function getTime()
    {
        $benchmark = Profiler::application();
        return 'Страница подготовлена за ' .
        round($benchmark['current']['time'], 5) .
        ' сек. На ' . $benchmark['count'] . ' загрузок страниц затрачено ' .
        round($benchmark['total']['time']) . ' сек.';
    }

    /**
     * Возвращает строку с полной статистикой работы приложения (для администратора)
     * @return string
     */
    public static function getAll()
    {
        $benchmark = Profiler::application();
        return 'Минимальное время загрузки страницы ' .
        $benchmark['min']['time'] . ' сек. (' .
        $benchmark['min']['memory'] . ' байт). ' .
        'Текущая страница подготовлена за ' .
        $benchmark['current']['time'] . ' сек. (' .
        $benchmark['current']['memory'] . ' байт). ' .
        'Максимальное время ' . $benchmark['max']['time'] . ' сек. (' .
        $benchmark['max']['memory'] . ' байт). ' .
        'Страницы загружались ' . $benchmark['count'] . ' раз (' .
        $benchmark['total']['time'] . ' сек. ' .
        $benchmark['total']['memory'] . ' байт).';
    }

    /**
     * Возвращает время загрузки страницы
     * @return float
     */
    public static function getTimeSec()
    {
        $benchmark = Profiler::application();
        return round($benchmark['current']['time'], 3);
    }
}