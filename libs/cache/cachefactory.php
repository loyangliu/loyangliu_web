<?php

if (defined('CACHE_FACTORY_PHP'))
{
	return;
}
else
{
	define('CACHE_FACTORY_PHP', 1);
}

include_once 'cache.php';

class CacheFactory
{
	private static $_this = null;
	
	public static $cacheset = array();
	
	private static function openCache()
	{
		config('cache');
		$cacheset = & CacheFactory::$cacheset;
		
		foreach (CACHE_CONFIG::$caches as $key=>$cacheconfig)
		{
			if(!isset($cacheset[$key]))
			{
				$cacheset[$key] = Cache::instance($cacheconfig);
			}
		}
	}
	
	public static function instance()
	{
		if(CacheFactory::$_this == null)
		{
			$_this = new CacheFactory();
		}
		return $_this;
	}
	
	private function __construct()
	{
		CacheFactory::openCache();
	}
	
	public function createRedisCache()
	{
		if(!isset(CacheFactory::$cacheset['redis']))
		{
			throw new Exception('cannot create redis cache.');
		}
		
		return CacheFactory::$cacheset['redis'];
	}
}