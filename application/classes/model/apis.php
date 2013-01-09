<?php defined('SYSPATH') or die('No direct script access.');

class Model_Apis
{
    public static function get($api='')
    {
        if('' == $api)
        {
            $array = DB::select()
                    ->from('codes')
                    ->execute()
                    ->as_array();
            $apis = array();
            foreach ($array as $value) 
            {
                $apis[$value['service']] = $value['code'];
            }
            return $apis;
        }
            
        $select = DB::select('code')
                    ->from('codes')
                    ->where('service', '=', $api)
                    ->limit(1)
                    ->execute()
                    ->as_array();
        return isset($select[0]['code'])? $select[0]['code'] : '';
    }

    public static function set($api,$value)
    {
        return DB::update('codes')
                    ->value('code', $value)
                    ->where('service', '=', $api)
                    ->limit(1)
                    ->execute();
    }
}