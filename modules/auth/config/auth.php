<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'driver' => 'ORM',
	'hash_method' => 'md5',
	'salt_pattern' => '',
	'lifetime' => 1209600,
	'session_key' => 'auth_user',
	'users' => array()
);
