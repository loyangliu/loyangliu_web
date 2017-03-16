<?php


if (defined('LIBS_REDISCACHE_PHP')) {
	return;
} else {
	define('LIBS_REDISCACHE_PHP', 1);
}

include_once 'cache.php';

class RedisCache extends Cache {
	private $defaultLifeTime = 86400;
	private $redis;
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function __destruct()
	{
		$this->redis->close();
	}
	
	public function init() {
		config('cache');
		$this->defaultLifeTime = $this->dsn['defaultLifeTime'];
		
		$this->redis = new Redis();
		$this->redis->connect($this->dsn['ip'], $this->dsn['port']);
		$this->redis->auth($this->dsn['pass']);
	}
	

	public function get($key, $id) {
		return json_decode($this->redis->get($key), true);
	}
	

	public function set($key, $value, $lifeTime = 0) {
		$lifeTime = $lifeTime ? $lifeTime : $this->defaultLifeTime;
		return $this->redis->set($key, json_encode($value), $lifeTime);
	}
	

	public function delete($key) {
		return $this->redis->del($key);
	}
	
	public function gc(){
		return;
	}

	public function cacheObject(){
		return $this->redis;
	}
}



// end of script