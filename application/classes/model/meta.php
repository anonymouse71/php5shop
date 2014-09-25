<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Meta extends ORM
{
    /**
     * Fields:
     *      `path`          - page's URI
     *      `title`         - title tag
     *      `keywords`      - meta keywords
     *      `description`   - meta description
     */

    protected static $meta_set = null;
    protected static $meta = null;

    /**
     * Returns meta data of current page, or FALSE
     * @return mixed
     */
    public static function get_current()
    {
        $base = url::base();
        $uri = urldecode($_SERVER['REQUEST_URI']);
        if ($base != '/' && $uri != '/')
            $uri = '/' . mb_substr($uri, mb_strlen($base, Kohana::$charset));

        self::$meta = ORM::factory('meta')->where('path', '=', $uri)->find();
         self::$meta_set = (bool)self::$meta->id;
        self::$meta_set = (bool)self::$meta->id;
        return self::$meta;
    }

    /**
     * meta tags was set for current page
     * @return bool
     */
    public static function special_meta_tags()
    {
        if (empty(self::$meta_set))
            self::get_current();
        return self::$meta_set;
    }

    /**
     * returns meta tag or title
     * @param $key
     * @return string
     */
    public static function get_meta($key)
    {
        if (!self::special_meta_tags() || !in_array($key, array('title', 'keywords', 'description')))
            return '';
        return self::$meta->$key;
    }

}