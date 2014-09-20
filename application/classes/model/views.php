<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Информация о последних просмотренных пользователем товарах
 */
class Model_Views
{
    /**
     * @param int $user user_id
     * @param int $limit
     * @return mixed
     */
    public static function last_products($user, $limit)
    {
        $views = DB::select('user_views.id', 'product_id', 'name', 'products.path')
            ->from('user_views')
            ->join('products')->on('product_id', '=', 'products.id')
            ->where('user_id', '=', $user)
            ->order_by('user_views.id', 'desc')
            ->limit($limit)->execute()->as_array();

        //удаляем более старые, так как они все равно не нужны
        if (count($views) == $limit)
        {
            DB::delete('user_views')->where('user_id', '=', $user)
                ->and_where('id', '<', $views[$limit - 1]['id'])->execute();
        }
        return $views;
    }
}