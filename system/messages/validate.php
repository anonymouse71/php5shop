<?php defined('SYSPATH') or die('No direct script access.');

return array(
	'not_empty'    => ': поле не должно быть пустым',
	'matches'      => ': должен совпадать с полем :param1',
	'regex'        => ': поле не отвечает формату',
	'exact_length' => ': поле должно состоять из :param1 символов',
	'min_length'   => ': введено меньше :param1 символов',
	'max_length'   => ': введено больше :param1 символов',
	'in_array'     => ': field must be one of the available options',
	'digit'        => ': field must be a digit',
	'decimal'      => ': field must be a decimal with :param1 places',
	'range'        => ': field must be within the range of :param1 to :param2',
	'validate::email'=>': не настоящий',
	'username_available'=>': уже занято',
	'email_available' => ': уже используется',
	'captcha_invalid'=> ': Проверочное изображение введено неверно',
	'validate::phone'=>': не настоящий',
        'validate::digit'=>'id родительской категории не отвечает числовому формату'
);
