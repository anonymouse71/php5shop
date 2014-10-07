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

class Model_Sitemap
{
    public function update()
    {
        @file_put_contents($_SERVER['DOCUMENT_ROOT'] . url::base() . 'sitemap.xml', $this->get());
        $this->pingIt();
    }

    public function get()
    {
        $site = 'http://' . $_SERVER['HTTP_HOST'];
        $urls = '';

        $controllers = array_merge(array('', 'shop/blog', 'about'), array_keys(Model_Page::get_menu()));

        foreach ($controllers as $page)
            $urls .= $this->url($site .  url::base() . $page);

        foreach (ORM::factory('product')->find_all() as $product)
            $urls .= $this->url($site . $product->getUri());
        foreach (DB::select('path')->from('categories')->execute()->as_array(null, 'path') as $cat_path)
            $urls .= $this->url($site . Categories::getUriByPath($cat_path));
        foreach (ORM::factory('blogPost')->find_all() as $blog)
            $urls .= $this->url($site . url::base() . 'blog/' . $blog);

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
        '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" .
        $urls .
        '</urlset>';
    }

    private function url($loc)
    {
        return '   <url>' . "\n" .
        '       <loc>' . $loc . '</loc>' . "\n" .
        '   </url>' . "\n";
    }

    private function pingIt()
    {
        $urls = array(
            'http://google.com/webmasters/sitemaps/ping?sitemap=',
            'http://webmaster.yandex.ru/wmconsole/sitemap_list.xml?host=',
            'http://webmaster.live.com/ping.aspx?siteMap='
        );

        foreach ($urls as $url)
        {
            $curl = curl_init($url . 'http://' . $_SERVER['HTTP_HOST'] . url::base() . 'sitemap.xml');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 2);
            curl_exec($curl);
            curl_close($curl);
        }
    }
}