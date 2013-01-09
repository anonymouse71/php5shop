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

/**
 * Класс для создания RSS ленты
 */

class Model_Rss {

    /**
     * Функция для создания RSS файлов
     * @param string $title - имя RSS канала
     * @param string $link - ссылка на feed
     * @param string $description - описание
     * @param string $email - почтовый адрес администратора
     * @param array $items - массив записей, каждая содержит: 'title','link','description','pubDate','guid'
     * @return string - xml код RSS канала, который желательно записывать в xml файл
     */
    public static function feed($title, $link, $description, $email, $items)
    {
        $date = date('r');               //date("D, d M Y H:i:s T");
        $rn = "\r\n";
        $return = '<' . '?xml version="1.0" encoding="UTF-8" ?' . '>' . $rn .
        '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . $rn .
        '  <channel>' . $rn .
        '    <title>' . $title . '</title>' . $rn .
        '    <atom:link href="' . $link . '" rel="self" type="application/rss+xml" />' . $rn .
        '    <link>' . $link . '</link>' . $rn .
        '    <description>' . $description . '</description>' . $rn .
        '    <language>ru</language>' . $rn .
        '    <pubDate>' . $date . '</pubDate>' . $rn . $rn .
        '    <lastBuildDate>' . $date . '</lastBuildDate>' . $rn .
        '    <docs>http://blogs.law.harvard.edu/tech/rss</docs>' . $rn .
        '    <generator>php 5</generator>' . $rn .
        '    <managingEditor>phpdreamer@rambler.ru (phpdreamer )</managingEditor>' . $rn .
        '    <webMaster>' . $email . '</webMaster>' . $rn . $rn ;

        foreach($items as $item)
            $return .=
            '    <item>' . $rn .
            '      <title>' . $item['title'] . '</title>' . $rn .
            '      <link>' . $item['link'] . '</link>' . $rn .
            '      <description>' . $item['description'] . '</description>' . $rn .
            '      <pubDate>' . $item['pubDate'] . '</pubDate>' . $rn .
            '      <guid>' . $item['guid'] . '</guid>' . $rn .
            '    </item>' . $rn . $rn ;

        return $return . '  </channel>' . $rn . '</rss>';
    }

}