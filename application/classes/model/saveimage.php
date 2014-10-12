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
 * Модель сохранения изображений на сервере
 *
 *      $img = ORM::factory('saveImage');
 *      $img->saveit($url_1,$id_1); //Сначало создается очередь изображений
 *      $img->saveit($url_2,$id_2);
 *
 *      $img->init();               //а затем запускается обработка очереди в фоновом режиме
 */
class Model_SaveImage extends ORM
{

    public $ImgSize = 500; //размер изображений
    public $ImgSizeSmall = 150; //размер превью

    /**
     * Сохраняет изображения во временную очередь
     * @param string $url
     * @param int $id
     * @param int $n
     */
    public function saveit($url, $id, $n = 0)
    {
        $img = ORM::factory('saveImage');
        $img->__set('id', (int)$id);
        $img->__set('url', $url);
        $img->__set('n', (int)$n);
        $img->save();
    }

    /**
     * Сохраняет изображения на диске. Добавляет watermark и уменьшает до нужного размера
     * @param string $url
     * @param string $id
     * @return bool
     */
    public function gd($url, $id)
    { //если можно использовать file_get_contents
        if (0 === strpos($url, $_SERVER['DOCUMENT_ROOT']))
            $conents = file_get_contents($url);
        else //если нельзя, используем curl
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $conents = curl_exec($curl);
            curl_close($curl);
        }
        if (!$conents)
            return FALSE;
        $filename = APPPATH . 'cache/' . text::random('alnum', 12) . '.jpg';
        file_put_contents($filename, $conents);
        unset($conents);

        $this->gdFile($filename, $id);

        unlink($filename);
    }

    public function gdFile($filename, $id)
    {
        $smallImg = DOCROOT . 'images/products/small/' . $id . '.jpg';
        $bigImg = DOCROOT . 'images/products/' . $id . '.jpg';

        try
        {
            $image = new Kohana_Image_GD($filename);
        } catch (Kohana_Exception $ke)
        {
            return FALSE;
        }
        $image->resize($this->ImgSize, $this->ImgSize);
        $img = imagecreatefromstring($image->render());
        $mask = imagecreatefrompng(DOCROOT . 'images/watermark.png');
        imagecopy($img, $mask, 0, 0, 0, 0, $this->ImgSize, $this->ImgSize);
        imagejpeg($img, $bigImg, 95);

//        if((string)$id == (string)(int)$id)                                     //маленькие изображения сохраняем только для главной фотографии
//        {
        $image->resize($this->ImgSizeSmall, $this->ImgSizeSmall);
        $image->save($smallImg);
        @chmod($smallImg, 0666);
//        }

        @chmod($bigImg, 0666);
        return TRUE;

    }

    /**
     * Запускает рекурсивную обработку очереди изображений на сохранение.
     */
    public function init()
    {
        $curl = curl_init('http://' . $_SERVER['HTTP_HOST'] . url::base() . 'save/img');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        curl_exec($curl);
        curl_close($curl);

    }

    public static function ckeditor_image_upload()
    {
        $http_path = '';
        $error = '';
        if (!isset($_GET['CKEditorFuncNum'], $_FILES['upload']))
        {
            $error = 'Bad Request!';
            $callback = '"function(){alert(\'' . $error . '\')}"';
        }
        else
        {
            $folder = 'user-img/';
            $callback = $_GET['CKEditorFuncNum'];
            $file_name = $_FILES['upload']['name'];
            $ext = explode('.', $file_name);
            $ext = $ext[count($ext) - 1];
            if ($ext == 'php')
            {
                $error = 'Нельзя загружать php файлы.';
                Kohana_Log::instance()->add('SECURITY',
                    'Попытка загрузить PHP файл как изображение с IP ' . $_SERVER['REMOTE_ADDR']);
            }
            else
            {
                $file_name_tmp = $_FILES['upload']['tmp_name'];
                do
                {
                    $file_name = Kohana_Text::random('alnum', 16) . '.' . $ext;
                }
                while (file_exists($folder . $file_name));

                $full_path = $folder . $file_name;
                $moved = false;
                try
                {
                    $moved = move_uploaded_file($file_name_tmp, $full_path);
                    if (!$moved)
                    {
                        $error = 'Не удалось сохранить файл.';
                    }
                } catch (ErrorException $e)
                {

                    $error = 'Не удалось сохранить файл. ';
                    if (FALSE !== strpos($e, 'Permission'))
                        $error .= 'Необходимо установить права на запись в каталог ' . $folder;
                }
                if ($moved)
                {
                    try
                    {
                        new Kohana_Image_GD($full_path);
                        $http_path = '/' . $folder . $file_name;
                    }
                    catch (Kohana_Exception $ke)
                    {
                        unlink($full_path);
                        $error = 'Файл не является допустимым изображением.';
                    }
                }
            }

        }

        return "<script type=\"text/javascript\">window.parent.CKEDITOR.tools.callFunction("
        . $callback . ",  \"" . $http_path . "\", \"" . $error . "\" );</script>";
    }
}