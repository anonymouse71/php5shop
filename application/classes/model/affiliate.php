<?php defined('SYSPATH') or die('No direct script access.');

class Model_Affiliate
{
    public function percent($set=null)
    {
        if($set)
            return DB::update('affiliate')->value('percent', abs($set))
                ->where('percent', '>=', 0)
                ->limit(1)
                ->execute();
        else
            {
                $select = DB::select('percent')->from('affiliate')
                    ->where('percent', '>=', 0)
                    ->limit(1)
                    ->execute();
                return isset($select[0]['percent'])? $select[0]['percent'] : 0;
            }
    }

    public function about($set=null)
    {
        if($set)
            return DB::update('affiliate')->value('about', $set)
                ->where('percent', '>=', 0)
                ->limit(1)
                ->execute();
        else
            {
                $select = DB::select('about')->from('affiliate')
                    ->where('percent', '>=', 0)
                    ->limit(1)
                    ->execute();
                return isset($select[0]['about'])? $select[0]['about'] : null;
            }
    }
}