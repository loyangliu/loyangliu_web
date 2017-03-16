<?php

if(defined('CONFIG_CACHE_PHP'))
{
	return;
}
else
{
	define('CONFIG_CACHE_PHP', 1);
}


if(!defined('CACHE_FILE'))
{
	define('CACHE_FILE', LIBS_CACHE . DS . 'file.cache');
}

class CACHE_CONFIG
{
	public static $defaultCache = 'redis';
	
	public static $caches = Array(
		'file' => Array(
				'type' => 'file',
				'classname' => 'FileCache',
				'filename' => CACHE_FILE
				),
			
		'db' => Array(
				'type' => 'db',
				'classname' => 'DbCache',
				'table' => 'cache',
				),
			
		'redis' => Array(
				'type' => 'redis',
				'classname' => 'RedisCache',
				'ip' => '192.168.111.128',
				'port' => 6379,
				'pass' => 'liuyang',
				'defaultLifeTime' => 86400,
				)
	);
}

?>