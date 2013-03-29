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
 * с программой. В случае её отсутствия, посмотрите http://www.gnu.org/licenses/.
 */

//YML - Yandex Market Language @link http://help.yandex.ru/partnermarket/?id=1111425

class Model_Yml
{

    public static function get()
    {
        $currency = 'RUB';

        $xml = '<?xml version="1.0" encoding="utf-8"?>'
            . "\r\n". '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'
            . "\r\n". '<yml_catalog date="'. date('Y-m-d'). ' '. date('H:i'). '">'
            . "\r\n". '    <shop>'
            . "\r\n". '        <name></name>'
            . "\r\n". '        <company></company>'
            . "\r\n". '        <url></url>'
            . "\r\n". '        <currencies>'
            . "\r\n". '            <currency id="RUR" rate="1" plus="0"/>'
            . "\r\n". '        </currencies>'
            . "\r\n". '        <categories></categories>'
            . "\r\n". '        <offers></offers>'
            . "\r\n". '    </shop>'
            . "\r\n". '</yml_catalog>';

        $dom = new DomDocument;
        $dom->loadXML($xml);

        $shopName = ORM::factory('html')->getblock('shopName');
        self::setNodeVal($dom, 'name', $shopName);
        self::setNodeVal($dom, 'company', $shopName);
        $shopURL = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        self::setNodeVal($dom, 'url', $shopURL);

        $curr = Model::factory('config')->getCurrency($currency); //получение курса валют
        if (!$curr)
            $curr = 1;

        $catsNode = $dom->getElementsByTagName('categories')->item(0);
        $catsArray = DB::select()->from('categories')->order_by('id')
            ->execute()->as_array();
        foreach($catsArray as $catItem)
        {
            $cat = $dom->createElement('category', html_entity_decode($catItem['name']));
            $cat->setAttribute('id', $catItem['id']);
            if($catItem['parent'])
                $cat->setAttribute('parentId', $catItem['parent']);
            $catsNode->appendChild($cat);
        }

        $offersNode = $dom->getElementsByTagName('offers')->item(0);
        $prodArray = DB::select()->from('products')
            ->join('descriptions', 'left')->on('products.id', '=', 'descriptions.id')
            ->execute()
            ->as_array();
        foreach($prodArray as $prod)
        {
            $p = $dom->createElement('offer');
            $p->setAttribute('id', $prod['id']);
            $p->setAttribute('type', 'vendor.model');
            $p->setAttribute('available', $prod['whs']? 'true' : 'false');
            $p->appendChild($dom->createElement('url', $shopURL . 'shop/product' . $prod['id']));
            $p->appendChild($dom->createElement('price', round($curr * $prod['price'], 2)));
            $p->appendChild($dom->createElement('currencyId', 'RUR'));
            $p->appendChild($dom->createElement('categoryId', $prod['cat']));
            $p->appendChild($dom->createElement('picture', $shopURL . 'images/products/' . $prod['id'] .'.jpg'));
            $p->appendChild($dom->createElement('model', html_entity_decode($prod['name'])));
            $p->appendChild($dom->createElement('description', html_entity_decode($prod['text'])));

            $offersNode->appendChild($p);
        }

        return $dom->saveXML();
    }

    protected static function setNodeVal($dom, $key, $value)
    {
        $node = $dom->getElementsByTagName($key);
        if($node->length > 0)
            $node->item(0)->nodeValue = $value;
    }
}