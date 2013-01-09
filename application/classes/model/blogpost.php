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
 * ORM модель записей блога
 */
class Model_BlogPost extends ORM {

	// Правила для валидации
	protected $_rules = array
	(
		'title'			=> array                                //заголовок
		(
			'not_empty'		=> null,                        //не пуст
			'min_length'		=> array(4),                    //не меньше 4 симв.
			'max_length'		=> array(200)                   //не больше 200
		),
		'html'			=> array                                //код
		(
			'not_empty'		=> null,                        //не пуст
			'min_length'		=> array(5)                     //не меньше 5 симв.
		),
                'html2'			=> array                                //код
		(
			'not_empty'		=> null,                        //не пуст
			'min_length'		=> array(5)                     //не меньше 5 симв.
		),
                'id'			=> array
		(
			'not_empty'		=> null,                        
			'validate::digit'	=> null
		)
	);
/**
 * Добавляет новую запись в блог. Проводит валидацию и возвращает массив ошибок.
 * Заполняет поле даты.
 * @param array $array     - массив с 'title' и 'html' (заголовок и html код записи)
 * @return array           - массив ошибок
 */
        public function __add($array)
        {
            $array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('title', $this->_rules['title'])
			->rules('html', $this->_rules['html'])
                        ->rules('html2', $this->_rules['html2']);

            if ($array->check())                                                //успешная валидация?
            {
                $this->__set('title', $array['title']);                         //установка значений
                $this->__set('html', $array['html']);
                $this->__set('html2', $array['html2']);
                $this->__set('date', time());
                $this->save();                                                  //запись в БД
            }

            return $array->errors('Validate');
        }
/**
 * Обновляет записи. Проводит валидацию и возвращает массив ошибок.
 * Обновляет поле даты.
 * @param array $array     - массив с 'title' и 'html' (заголовок и html код записи)
 * @return array           - массив ошибок
 */
        public function __update($array)
        {
            $array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('title', $this->_rules['title'])
			->rules('html', $this->_rules['html'])
                        ->rules('html2', $this->_rules['html2'])
                        ->rules('id', $this->_rules['id']);

            if ($array->check())                                                //успешная валидация?
            {
                $model = ORM::factory('blogPost',$array['id']);
                $model->__set('title', $array['title']);                        //установка значений
                $model->__set('html', $array['html']);
                $model->__set('html2', $array['html2']);
                $model->__set('date', time());
                $model->save();                                                 //запись в БД
            }

            return $array->errors('Validate');
        }
/**
 * Читает последние $limit записей блога, с учетом страницы $page
 * @param int $page
 * @param int $limit
 * @return ORM
 */
        public function read($page=1,$limit=5)
        {
            return $this->order_by('id','desc')
                    ->limit($limit)
                    ->offset( ($page-1)*$limit )
                    ->find_all();
        }
/**
 * Читает последню запись в блоге, обрезает ее текст до $substr символов,
 * перед этим уберает форматирование.
 * Возвращает массив с id, title и code
 * @param int $substr
 * @return array
 */
        public function last($substr=null)
        {
            $last = $this->read(1, 1);
            if(!isset($last[0]))
                return array();
            $code = $last[0]->html2;
            if($substr)
            {
                $code = trim(strip_tags($code));
                if(mb_strlen($code, 'utf-8') > $substr)
                    $code = mb_substr($code, 0, $substr, 'utf-8') . '...';

            }
                
            return array('id'=>$last[0]->id, 'title'=>$last[0]->title,'code'=>$code);
        }

/**
 * Обновляет файл rss ленты
 * Пример вызова: Model::factory('BlogPost')->updateFeed('/rss.xml');
 * @param string $filename - адрес файла от корня сервера, например "/rss.xml"
 * @return bool
 */
        public function updateFeed($filename)
        {
            if(!is_writable($_SERVER['DOCUMENT_ROOT'] . $filename))
                return FALSE;
            $i = 0;
            $BlogPosts = $this->order_by('id','desc')->find_all();
            foreach($BlogPosts as $item)
            {
                $posts[$i]['title'] = htmlspecialchars($item->title);
                $posts[$i]['link'] = 'http://' . $_SERVER['HTTP_HOST'] .
                                     url::base() . 'shop/blog/' . $item->id;
                $posts[$i]['description'] = htmlspecialchars(trim(strip_tags($item->html2)));
//                if(mb_strlen($posts[$i]['description'], 'utf-8') > 300)
//                    $posts[$i]['description'] =
//                        mb_substr($posts[$i]['description'], 0, 300,'utf-8') .
//                        '...';
                
                $posts[$i]['pubDate'] = date('r',$item->date);
                $posts[$i]['guid'] = $posts[$i]['link'];
                $i++;
            }
            $email = Model::factory('html')->getblock('email');
            $shopName = Model::factory('html')->getblock('shopName');
            $about = Model::factory('html')->getblock('about');
            if(!$email)
                $email = 'webmaster@site.ru (webmaster)';
            if(!$shopName)
                $shopName = 'shop';
            if(!$about)
                $about = 'shop';

            $feed = Model::factory('rss')->feed(
                        htmlspecialchars(trim(strip_tags($shopName))),
                        'http://' . $_SERVER['HTTP_HOST'] . $filename,
                        htmlspecialchars(trim(strip_tags($about))),
                        $email . ' (webmaster)',
                        $posts
                    );
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . $filename, $feed);
            return TRUE;
        }
}