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

class Model_Product extends ORM
{

    /**
     * Возвращает товары из категорий $categories
     * в количестве $onPage начиная со страницы $page
     * @param array $categories
     * @param int $page
     * @param int $onPage
     * @return array
     */
    public function byCategory($categories, $page, $onPage)
    {
        $orderBy = self::getSort(self::getSort());
        return DB::select()->from('products')
            ->order_by($orderBy[0], $orderBy[1])
            ->limit($onPage)
            ->offset(($page - 1) * $onPage)
            ->where('cat', 'in', $categories)
            ->execute()
            ->as_array();
    }

    /**
     * Возвращает последние $n товаров
     * @param int $n
     * @param int $offset
     * @return array
     */
    public function getLast($n, $offset = 0)
    {
        return DB::select()->from('products')
            ->order_by('id', 'desc')
            ->offset($offset)
            ->limit($n)
            ->execute()
            ->as_array();
    }

    /**
     * Обновляет файл rss ленты
     * Пример вызова: Model::factory('product')->updateFeed('/rss.xml');
     * @param string $filename - адрес файла от корня сервера, например "/rss.xml"
     * @return bool
     */
    public function updateFeed($filename)
    {
        if (!is_writable($_SERVER['DOCUMENT_ROOT'] . $filename))
            return FALSE;
        $i = 0;
        foreach (self::getLast(10) as $item)
        {
            $posts[$i]['title'] = $item['name'];
            $posts[$i]['link'] = 'http://' . $_SERVER['HTTP_HOST'] .
                url::base() . 'shop/product' . $item['id'];

            $posts[$i]['description'] = strip_tags(ORM::factory('description', $item['id'])->__get('text'));

            $posts[$i]['pubDate'] = date('r');
            $posts[$i]['guid'] = $posts[$i]['link'];
            $i++;
        }
        $email = Model::factory('html')->getblock('email');
        $shopName = Model::factory('html')->getblock('shopName');
        $about = Model::factory('html')->getblock('about');
        if (!$email)
            $email = 'webmaster@site.ru (webmaster)';
        if (!$shopName)
            $shopName = 'shop';
        if (!$about)
            $about = 'shop';

        $feed = Model::factory('rss')->feed(
            $shopName,
            'http://' . $_SERVER['HTTP_HOST'] . $filename,
            $about,
            $email . ' (webmaster)',
            $posts
        );
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . $filename, $feed);
        return TRUE;
    }

    /*
     * Влияние на все цены
     * @param double $persent - процент который нужно добавить
     * @param array $exceptionCats - массив id категорий, которые исключаем
     */
    public static function changePrice($persent, $exceptionCats = array())
    {
        $update = DB::update('products'); //создаем запрос

        foreach ($exceptionCats as $exc) //добавляем условия
            $update->where('cat', '!=', (int)$exc);

        $sql = str_replace( //дополняем запрос
            'SET',
            'SET price = price + price * ' . number_format((double)$persent / 100, 10, '.', ''),
            $update->__toString() //предварительно конвертировав его из объекта в строку
        );

        DB::query('', $sql)->execute(); //выполняем полученый запрос

    }

    public static function getSort($id = 0)
    {
        if ($id == 0)
        {
            $sort = Session::instance()->get('sort');
            if (!$sort)
                $sort = 2;
            return $sort;
        }

        switch ($id)
        {
            case 1:
                return array('id', 'ASC');
            case 2:
                return array('id', 'DESC');
            case 3:
                return array('name', 'ASC');
            case 4:
                return array('name', 'DESC');
            case 6:
                return array('price', 'DESC');

            default: //5
                return array('price', 'ASC');
        }
    }

    /**
     * Удаление товара по id
     * @param int $id
     */
    public static function deleteProduct($id)
    {
        $id = (int)$id;
        $imagePath1 = $_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $id . '.jpg';
        $imagePath2 = $_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/small/' . $id . '.jpg';
        if (file_exists($imagePath1))
            unlink($imagePath1);
        if (file_exists($imagePath2))
            unlink($imagePath2);
        $n = 1;
        while (file_exists($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $id . '-' . $n . '.jpg') && $n < 100)
        {
            unlink($_SERVER['DOCUMENT_ROOT'] . url::base() . 'images/products/' . $id . '-' . $n . '.jpg');
            $n++;
        }
        DB::delete('user_views')->where('product_id', '=', $id)->execute();
        ORM::factory('rating_user')->where('product', '=', $id)->delete_all();
        ORM::factory('rating_value', $id)->delete();
        ORM::factory('product')->where('id', '=', $id)->delete_all();
        ORM::factory('description')->where('id', '=', $id)->delete_all();
    }

    /**
     * Устанавливает значение, в отличии от __set возвращает объект
     * @param string $column
     * @param string $value
     * @return Model_Product
     */
    public function set($column, $value)
    {
        $this->__set($column, $value);
        return $this;
    }

    /**
     * Импорт из xls - передается массив, прочитанный через xlsreader
     * Возвращает колич. обработанных
     * @param array $sheets
     * @return int
     */
    public static function importXls($sheets)
    {
        $imgObj = ORM::factory('saveImage');

        $i = 0; //счетчик добавленных товаров
        foreach ($sheets as $page)
            if (isset($page['cells']))
                foreach ($page['cells'] as $cell)
                {
                    $productId = isset($cell[1]) ? $cell[1] : 0; //номер товара
                    if ($productId == 'ID')
                        continue;
                    $catId = isset($cell[2]) ? $cell[2] : 0; //номер категории
                    $prodName = isset($cell[3]) ? $cell[3] : ''; //название
                    $prodDescr = isset($cell[4]) ? $cell[4] : ''; //полное описание

                    $prodPrice = isset($cell[5]) ? $cell[5] : 0; //цена
                    $prodPrice = str_replace(',', '.', $prodPrice);
                    $prodPrice = str_replace(' ', '', $prodPrice);

                    $imgs = isset($cell[6]) ? $cell[6] : ''; //изображения
                    $availability = isset($cell[7]) ? $cell[7] : 1; //наличие на складе (кол-во)

                    $title = isset($cell[8]) ? $cell[8] : '';
                    $metaDescription = isset($cell[9]) ? $cell[9] : '';
                    $metaKeywords = isset($cell[10]) ? $cell[10] : '';

                    if (!$catId) // категория не указана
                    { //товар будет удален
                        if ($productId)
                            Model_Product::deleteProduct($productId);
                    }
                    elseif ($prodName && $prodPrice)
                    { //добавление через ORM
                        if ($productId) //если в 1-м столбце есть id, работаем с товаром из БД
                        {
                            $product = ORM::factory('product', $productId);

                            if (!$product->id)
                            {
                                $product->__set('id', $productId);
                            }
                        }
                        else
                        {
                            $product = ORM::factory('product');
                        }

                        $product->set('cat', $catId)
                            ->set('name', $prodName)
                            ->set('price', $prodPrice)
                            ->set('whs', $availability)
                            ->save();

                        $id = $productId ? $productId : $product->__get('id');
                        if ($title || $metaDescription || $metaKeywords)
                        {
                            // сохраняем meta

                            $url = '/shop/product' . $id;
                            $meta = ORM::factory('meta')->where('path', '=', $url)->find();
                            if (!$meta->id)
                            {
                                $meta = ORM::factory('meta');
                                $meta->path = $url;
                            }

                            if ($title)
                                $meta->title = $title;
                            if ($metaDescription)
                                $meta->description = $metaDescription;
                            if ($metaKeywords)
                                $meta->keywords = $metaKeywords;
                            $meta->save();
                        }

                        if ($imgs) //обработка изображения
                        {
                            $images = explode(' ', $imgs); //разбиваем на массив ссылок
                            foreach ($images as $i_img => $img) //$i_img - номер ссылки, $img - URL
                                if ($i_img < 100) //сохраняем только первые 99 изображений одного товара (ограничено полем в БД)
                                    $imgObj->saveit($img, $id, $i_img);
                        }

                        if ($prodDescr) //обработка полного описания
                        {
                            $description = $productId ?
                                ORM::factory('description', $productId)
                                :
                                ORM::factory('description');
                            if (!$description->id)
                                $description->__set('id', $id);

                            $description->__set('text', $prodDescr);
                            $description->save();
                        }

                        $i++;
                    }
                }
        return $i;
    }
}