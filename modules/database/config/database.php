<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'default' => array
	(
		'type'       => 'mysql',
		'connection' => array(
			/**
			 * The following options are available for MySQL:
			 *
			 * string   hostname
			 * string   username
			 * string   password
			 * boolean  persistent
			 * string   database
			 *
			 * Ports and sockets may be appended to the hostname.
			 */
			'hostname'    =>    'localhost',
			'username'    =>    'root',
			'password'    =>    'password',
			'persistent' => FALSE,
			'database'    =>    'php5shop',
		),
		'table_prefix' => 'p5shp_',
		'charset'      => 'utf8',
		'caching'      => TRUE,
		'profiling'    => FALSE,
	)
);
