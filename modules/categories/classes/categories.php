<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Класс решает задачи организации хранения древовидной структуры категорий
 * и построения меню.
 *
 * @property string $code
 * @property mixed $categories
 * @property string $view
 * @property string $viewSelect
 * @property array $children
 * @property array $render
 *
 * @author phpdreamer
 * @created 16.08.2010
 * @modified 05.06.2015
 */
class Categories
{
    public $code;                                                               //возвращаемый код меню
    public $categories;                                                         //массив с категориями, который могут использовать другие классы, например для выборки товаров категории
    public $view = 'item';                                                      //представление для меню
    public $viewSelect = 'select';                                              //представление для select

    private $children;
    private $render;

    /**
     * @param array $catsArray
     */
    public function __construct($catsArray = null)
    {
        if ($catsArray == null)
            $catsArray = DB::select()->from('categories')->order_by('id')->execute()->as_array();

        $this->categories = array(
            'parents' => array(),
            'names' => array(),
            'level' => array(),
            'path' => array(),
        );
        $fields = array_keys($this->categories);
        foreach ($catsArray as $value)
        {
            $value['parents'] = $value['parent'];
            $value['names'] = $value['name'];
            foreach ($fields as $k)
                $this->categories[$k][$value['id']] = $value[$k];
        }
    }

    /**
     * Метод возвращает HTML код меню
     * (используется отображение MODPATH.'categories/views/item.php')
     * @param int $id
     * @return string
     */
    public function menu($id = 0)
    {
        $this->build(0, $id);                                                    //запуск рекурсивной функции
        return $this->code;                                                     //ф-я возвращает HTML код
    }

    /**
     * Возвращает HTML элемент "select", содержащий все категории с полными путями
     * Причем выбрана категория $id
     * @param int $id
     * @return string
     */
    public function select($id)
    {
	    $this->children = array();
	    $this->build_select(0, $id);
        return View::factory($this->viewSelect)
            ->set('cats', $this->children)
            ->set('selected', $id)
            ->render();
    }

	/**
	 * Рекурсивная функция для построения select меню.
	 * Принимает id категории
	 *
	 * @param int    $id
	 */
	protected function build_select($id = 0)
	{
		if (!is_array($this->categories))
			return;
		if ($id)
		{
			$string = htmlspecialchars($this->categories['names'][$id]);
			$cid = $id;
			while ($parent = $this->categories['parents'][$cid])
			{
				$string = "&nbsp;&nbsp;" . $string;
				$cid = $parent;
			}
			$this->children[] = array('id' => $id, 'label' => $string);
		}
		else // первый пункт - ничего не выбрано
			$this->children[] = array('id' => 0, 'label' => '');
		foreach ($this->categories['parents'] as $PerentId => $PerentParentId)
			if ($PerentParentId == $id)
				$this->build_select($PerentId);
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
            $level++;
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
     * @param $path
     * @return array
     */
    public static function add($parent, $name, $path)
    {
        $name = Security::xss_clean($name);
        $path = str_replace('/', '-', $path);
        $array = self::getValidator($name, $path);
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
        } else
            $level = 0;

        if (count(DB::select()->from('categories')->where('path', '=', $path)->limit(1)->execute()->as_array()))
            return array('' => 'Адрес уже занят');

        $insert = DB::insert('categories', array('parent', 'level', 'name', 'path'))
            ->values(array($parent, $level, $name, $path))->execute();
        if ($insert)
            return array();
        else
            return array('' => 'Ошибка! категория не добавлена');
    }

    /**
     * Возвращает объект валидатор
     * @param $name
     * @param $path
     * @return Validate
     */
    protected static function getValidator($name, $path)
    {
        $rules['name'] = array(
            'not_empty' => NULL,
            'max_length' => array(50),
        );
        $rules['path'] = array(
            'not_empty' => NULL,
            'max_length' => array(255),
        );
        return Validate::factory(array( 'name' => $name, 'path' => $path))
            ->filter(TRUE, 'trim')

            ->rules('path', $rules['path'])
            ->rules('name', $rules['name']);
    }

    /**
     * Устанавливает новые значения для категории $id
     * Возвращает статус операции (TRUE или FALSE)
     * @param int $id
     * @param int $parent
     * @param string $name
     * @param $path
     * @return array of errors
     */
    public function update($id, $parent, $name, $path)
    {
        $name = Security::xss_clean($name);
        $path = str_replace('/', '-', $path);
        $id = (int)$id;
        $parent = (int)$parent;
        $validator = self::getValidator($name, $path);
        if (!$id || !$validator->check())
            return $validator->errors();

        if ($this->categories['path'][$id] != $path)
        {
            foreach ($this->categories['path'] as $cId => $cPath)
                if ($cPath == $path && $cId != $id)
                    return array('' => 'Путь "' . $path . '" уже используется другой категорией (' . $cId . ').');
        }

        $wasLevel = $this->categories['level'][$id];

        if ($parent >= 0)
        {
            if ($parent)
            {
                if (!isset($this->categories['level'][$parent]))
                    return array('' => 'Родительская категория id ' . $parent . ' не найдена.');
                $level = $this->categories['level'][$parent] + 1;
            }
            else
                $level = 0;



            if ($wasLevel != $level)
            {
                $children = $this->getCatChildren($id);
                if (in_array($parent, $children))
                    return array('' => 'Категория не может быть перенесена "в себя".');

                array_pop($children);
                $add = $level - $wasLevel;
                foreach ($children as $id_)
                {
                    $newLevel = $this->categories['level'][$id_] + $add;
                    DB::update('categories')
                        ->value('level', $newLevel)
                        ->where('id', '=', $id_)
                        ->limit(1)
                        ->execute();
                }
            }
        }
        else
        {
            $parent = $this->categories['parents'][$id];
            $level = $wasLevel;
        }

        if ($this->categories['path'][$id] != $path)
        {
            $uri_old = self::getUriByPath($this->categories['path'][$id]);
            $uri_new = self::getUriByPath($path);
            DB::update('metas')->set(array('path' => $uri_new))->where('path', '=', $uri_old)->execute();
        }

        if ((bool)DB::update('categories')
            ->value('parent', $parent)
            ->value('level', $level)
            ->value('path', $path)
            ->value('name', $name)
            ->where('id', '=', $id)
            ->execute()
        ) return array();
        else // затронуто 0 строк
            return array('' => 'Изменений не произошло.');
    }

    /**
     * Удаляет категорию по id
     * @param int $id
     * @return bool
     */
    public static function delete($id)
    {
        $path = DB::select('path')->from('categories')
            ->where('id', '=', $id)
            ->execute()->as_array(null, 'path');
        if (!count($path))
            return TRUE;
        $uri = self::getUriByPath($path[0]);
        DB::delete('metas')->where('path', '=', $uri)->execute();

        return (bool)DB::delete('categories')
            ->where('id', '=', $id)
            ->execute();
    }

    /**
     * Возвращает адрес страницы
     * @param $id
     * @param string $path
     * @return string
     */
    public function getUri($id, $path=null)
    {
        if (!$path && !isset($this->categories['path'][$id]))
            return url::base();
        if (!$path)
            $path = $this->categories['path'][$id];
        return url::base() . 'category/' . htmlspecialchars($path);
    }

    /**
     * @param $path
     * @return string
     */
    public static function getUriByPath($path)
    {
        $c = new self(array());
        return $c->getUri(0, $path);
    }

    /**
     * Возвращает массив id подкатегорий заданной категории
     * Первый элемент массива это id заданной категории
     * Если такой категории нет, возвращает пустой массив
     * @param int $id
     * @return array
     */
    public function getCatChildren($id)
    {
        if (!isset($this->categories['level'][$id]))
            return array();
        $this->children = array();
        $this->children[] = $id;
        $this->getChildren($id);
        return $this->children;
    }

    /**
     * Рекурсивно заполняет массив $this->childs подкатегориями для категории $id
     * @param int $id
     */
    protected function getChildren($id)
    {
        foreach ($this->categories['parents'] as $n => $cat)
            if ($cat == $id)
            {
                $this->children[] = $n;
                $this->getChildren($n);
            }
    }

    /**
     * Рекурсивная функция для построения меню.
     * Принимает id категории
     * @param int $id
     * @param int $boldId - текущая категория для выделения тегом <b>
     */
    protected function build($id = 0, $boldId = 0)
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
    protected function render($id, $name, $level, $bold = FALSE)
    {
        $v = View::factory($this->view)
            ->set('id', $id)
            ->set('name', $name)
            ->set('level', $level)
            ->set('path', $this->getUri($id));
        if ($bold)
            $v->set('tag', 'b');
        return $v->render();
    }

    public function menu2($id = 0)
    {
        $this->build2(0);
        $this->__render(0, $id);
        return View::factory('menu')->set('menu', $this->code)->render();
    }

    protected function build2($id = 0)
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

    protected function __render($id, $boldId = 0)
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
            if ($r['parent'] == $id)
            {
                if ($r['id'] == $boldId)
                    $this->code .= '<li><b><a href="' . $this->getUri($r['id'])
                        . '">' . $r['name'] . '</a></b>';
                else
                    $this->code .= '<li><a href="' . $this->getUri($r['id'])
                        . '">' . $r['name'] . '</a>';
                $this->__render($r['id'], $boldId);
                $this->code .= '</li>';
            }
        $this->code .= '</ul>';
    }

}