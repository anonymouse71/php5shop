<?php defined('SYSPATH') or die('No direct script access.');

class Model_Sape_client
{
    public static function links($code)
    {        
        if(!strlen($code))
            return '';                     //нет кода в БД
        elseif(!defined('_SAPE_USER'))
            define('_SAPE_USER', $code);

        $sapeFile = $_SERVER['DOCUMENT_ROOT'] . '/' . _SAPE_USER . '/sape.php';

        if(@file_exists($sapeFile))
            require($sapeFile);
        else
            return '';                     //нет файла sape.php
        $o['force_show_code'] = true;
        $sape = new SAPE_client($o);
        return '<div id="sape">' .
               iconv('cp1251', 'utf-8',  $sape->return_links()) .
               '</div>';
    }
}
