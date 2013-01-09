<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Класс решает задачи организации хранения древовидной структуры категорий в СУБД
 * и построения меню из этих категорий.
 *
 * phpdreamer
 * 16.08.2010
 * 04.05.2012
 */
class Categories
{

    public $code;                                                               //возвращаемый код меню
    public $categories;                                                         //массив с категориями, который могут использовать другие классы, например для выборки товаров категории
    public $view = 'item';                                                      //представление для меню
    public $viewSelect = 'select';                                              //представление для select
    public $path = '/shop/cat/';                                                //путь для построения ссылок
                                        //- если путь /shop/cat/ , то ссылки на категории будут /shop/cat/{id}
    private $childs;

    public function __construct($catsArray=null)
    {
        if ($catsArray == null)
            $catsArray = DB::select()->from('categories')->order_by('id')
                            ->execute()->as_array();

        foreach ($catsArray as $value)                                          //информация разделяется на 3 массива (для удобства)
        {
            $parents[$value['id']] = $value['parent'];
            $names[$value['id']] = $value['name'];
            $level[$value['id']] = $value['level'];
        }
        if (isset($parents))
            $this->categories = array(
                'parents' => $parents,
                'names' => $names,
                'level' => $level
            );
        else
            $this->categories = array(
                'parents' => array(),
                'names' => array(),
                'level' => array()
            );

    }

    /**
     * Метод возвращает HTML код меню
     * (используется отображение MODPATH.'categories/views/item.php')
     * @param string $path - путь к контроллеру категорий от корня виртуального сервера
     * @return string
     */
    public function menu($path, $id=0)
    {
        $this->path = $path;                                                    //переданный $path устанавливает свойство класса
        $this->build(0, $id);                                                    //запуск рекурсивной функции
        return $this->code;                                                     //ф-я возвращает HTML код
    }

    /**
     * Возвращает HTML элемент "select", содержащий все категории с полными путями
     * Причем выбрана категория $id
     * @param int $id
     * @param string $delimiter - разделитель
     */
    public function select($id, $delimiter='->')
    {
        $cats[0] = '';
        if (!isset($this->categories['names']))
            return;
        foreach ($this->categories['names'] as $CatId => $name)
        {
            $string = $this->categories['names'][$CatId];
            $cid = $CatId;
            while ($parent = $this->categories['parents'][$cid])
            {
                $string = $this->categories['names'][$parent] . $delimiter . $string;
                $cid = $parent;
            }
            $cats[$CatId] = $string;
        }
        return View::factory($this->viewSelect)
                        ->set('cats', $cats)
                        ->set('selected', $id)
                        ->render();
    }

    /**
     * Возвращает уровень вложенности категории по id
     * @param int $id
     * @return int
     */
    public static function getLevel($id)
    {
        $level = 0;
        while (0 < $id = self::getParent($id))
        {
            $level++;
        }
        return $level;
    }

    /**
     * Возвращает родительскую категорию для категории $id
     * Если категории $id не существует, возвращает -1
     * @param int $id
     * @return int
     */
    public static function getParent($id)
    {
        $array = DB::select('parent')->from('categories')
                ->where('id', '=', $id)
                ->limit(1)
                ->execute()
                ->as_array();

        return isset($array[0]['parent']) ? $array[0]['parent'] : -1;
    }

    /**
     * Добавляет категорию с именем $name, дочернюю для $parent
     * @param int $parent
     * @param string $name
     * @return array
     */
    public static function add($parent, $name)
    {
        $name = Security::xss_clean($name);
        $array = array('parent' => $parent, 'name' => $name);
        $rules['parent'] = array(
            'not_empty' => NULL,
            'validate::digit' => NULL,
        );
        $rules['name'] = array(
            'not_empty' => NULL,
            'max_length' => array(50),
        );
        $array = Validate::factory($array)
                ->filter(TRUE, 'trim')
                ->rules('parent', $rules['parent'])
                ->rules('name', $rules['name']);
        if (!$array->check())
            return $array->errors('validate');

        if ($parent)
        {
            $obj = DB::select()->from('categories')
                    ->where('id', '=', $parent)
                    ->limit(1)
                    ->execute()
                    ->as_array('id');

            if (!isset($obj[$parent]['id']))
                return array('' => 'Родительская категория не найдена');

            $level = self::getLevel($parent) + 1;
        }
        else
            $level = 0;

        $insert = DB::insert('categories', array('parent', 'level', 'name'))
                ->values(array($parent, $level, $name))
                ->execute();
        if ($insert)
            return array();
        else
            return array('' => 'Ошибка! категория не добавлена');
    }

    /**
     * Устанавливает новые значения для категории $id
     * Возвращает статус операции (TRUE или FALSE)
     * @param int $id
     * @param int $parent
     * @param string $name
     * @return bool
     */
    public function update($id, $parent, $name)
    {
        $id = (int) $id;
        $parent = (int) $parent;
        if ($id == 0)
            return;
        if ($parent)
        {
            if (!isset($this->categories['level'][$parent]))
                return FALSE;
            $level = $this->categories['level'][$parent] + 1;
        }
        else
            $level = 0;
        $wasLevel = $this->categories['level'][$id];

        if ($wasLevel != $level)
        {
            $childs = $this->getCatChilds($id);
            if (in_array($parent, $childs))
                return FALSE;
            array_pop($childs);
            $add = $level - $wasLevel;
            foreach ($childs as $id_)
            {
                $newLevel = $this->categories['level'][$id_] + $add;
                DB::update('categories')
                        ->value('level', $newLevel)
                        ->where('id', '=', $id_)
                        ->limit(1)
                        ->execute();
            }
        }
        DB::update('categories')
                ->value('parent', $parent)
                ->value('level', $level)
                ->value('name', Security::xss_clean($name))
                ->where('id', '=', $id)
                ->execute();
    }

    /**
     * Удаляет категорию по id
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        return DB::delete('categories')
                        ->where('id', '=', $id)
                        ->execute();
    }

    /**
     * Возвращает массив id подкатегорий заданной категории
     * Первый элемент массива это id заданной категории
     * Если такой категории нет, возвращает пустой массив
     * @param int $id
     * @return array
     */
    public function getCatChilds($id)
    {
        if (!isset($this->categories['level'][$id]))
            return array();
        $this->childs = array();
        $this->childs[] = $id;
        $this->getChilds($id);
        return $this->childs;
    }

    /**
     * Рекурсивно заполняет массив $this->childs подкатегориями для категории $id
     * @param int $id
     */
    protected function getChilds($id)
    {
        foreach ($this->categories['parents'] as $n => $cat)
            if ($cat == $id)
            {
                $this->childs[] = $n;
                $this->getChilds($n);
            }
    }

    /**
     * Рекурсивная функция для построения меню.
     * Принимает id категории
     * @param int $id
     * @param int $boldId - текущая категория для выделения тегом <b>
     */
    protected function build($id=0, $boldId=0)
    {
        if (!is_array($this->categories))
            return;
        if ($id && $boldId == $id)
            $this->code .= $this->render($id, $this->categories['names'][$id], $this->categories['level'][$id], TRUE);
        elseif ($id)
            $this->code .= $this->render($id, $this->categories['names'][$id], $this->categories['level'][$id], FALSE);
        foreach ($this->categories['parents'] as $PerentId => $PerentParentId)
            if ($PerentParentId == $id)
                $this->build($PerentId, $boldId);
    }

    /**
     * Подставляет id, имя и уровень вложенности категории в отображение $this->view
     * Возвращает строку меню
     * @param int $id
     * @param string $name
     * @param int $level
     * @param bool $bold
     * @return string
     */
    protected function render($id, $name, $level, $bold=FALSE)
    {
        $v = View::factory($this->view)
                ->set('id', $id)
                ->set('name', $name)
                ->set('level', $level)
                ->set('path', $this->path);
        if ($bold)
            $v->set('tag', 'b');
        return $v->render();
    }

    public function menu2($path, $id=0)
    {
        $this->path = $path;
        $this->build2(0);
        $this->__render(0, $id);
        return View::factory('menu')->set('menu', $this->code)->render();
    }

    protected function build2($id=0)
    {
        if (!is_array($this->categories))
            return;
        $parent = 0;
        foreach ($this->categories['parents'] as $PerentId => $PerentParentId)
            if ($PerentId == $id)
                $parent = $PerentParentId;
        if ($id)
            $this->render[] = array('id' => $id, 'name' => $this->categories['names'][$id], 'parent' => $parent);
        foreach ($this->categories['parents'] as $PerentId => $PerentParentId)
            if ($PerentParentId == $id)
                $this->build2($PerentId);
    }

    protected function __render($id, $boldId=0)
    {
        if (!isset($this->render))
            return;

        $flag = FALSE;
        foreach ($this->render as $r)
            if ($r['parent'] == $id)
                $flag = TRUE;
        if (!$flag)
            return;

        if ($id == 0)
            $this->code .= '<ul id="my-menu" class="cat-menu">';
        else
            $this->code .= '<ul>';

        foreach ($this->render as $r)
        {
            if ($r['parent'] == $id)
            {
                if ($r['id'] == $boldId)
                    $this->code .= '<li><b><a href="/shop/category' . $r['id']
                        . '">' . $r['name'] . '</a></b>';
                else
                    $this->code .= '<li><a href="/shop/category' . $r['id']
                        . '">' . $r['name'] . '</a>';
                $this->__render($r['id'], $boldId);
                $this->code .= '</li>';
            }
        }
        $this->code .= '</ul>';
    }

}