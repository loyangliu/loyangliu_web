<?php


if (defined('LIBS_CACHE_PHP'))
{
	return;
}
else
{
	define('LIBS_CACHE_PHP', 1);
}

define ( 'TIMESTAMP', time () ); // unix timestamp

  /**
   * file-cache, save global data
   * 
   */

abstract class Cache
{
	protected $dsn;
	
	protected function __construct()
	{
		$this->dsn = null;
	}
	
	public static function & instance($dsn)
	{
		$_this = null;
		
		libs('cache' . DS . lower($dsn['type']) . 'cache');
		$classname = $dsn['classname'];
		
		if(!class_exists($classname))
		{
			ethrow("unsupport cache type: {$engine['type']}");
		}
		
		if(!$_this)
		{
			$_this = new $classname;
			$_this->dsn = $dsn;
			$_this->init();
		}

		return $_this;
	}
	
	public abstract function init();
	public abstract function get($key, $id);
	public abstract function set($key, $data, $lifeTime = 0);
	public abstract function delete($key);
	public abstract function gc();
}



// end of script