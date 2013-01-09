<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Groups_user extends ORM {

    protected $_has_one = array('group' => array('model' => 'group'));

}