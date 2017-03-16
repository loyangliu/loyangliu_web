<?php


if (defined('LIBS_DBCACHE_PHP')) {
	return;
} else {
	define('LIBS_DBCACHE_PHP', 1);
}

include_once 'cache.php';
include_once LIBS_DB . DS . 'dbo.php';

class DBCache extends Cache {
	protected $db;
	protected $table;
	
	public function __construct()
	{
		parent::__construct();
	}

	public function init() {
		config('database');
		$dsn = DATABASE_CONFIG::$databases['default'];
		$this->db = & DBO::instance($dsn);
		$this->table = $this->dsn['table'];
	}
	

	public function get($key, $id) {
		$now = TIMESTAMP;
		$data = $this->db->getRow("select timeSet, timeExpire, value from `{$this->table}` where `key` = '$key' and timeExpire > '{$now}'", MYSQL_ASSOC);
	    if(!isset($data['value'])) {
			return false;
		}
		return json_decode($data['value'], true);
	}
	

	public function set($key, $data, $lifeTime = 0) {
		if($lifeTime > 0) {
			$timeExpire = TIMESTAMP + $lifeTime;
		} else {
			$timeExpire = 0;
		}
		
		$dataDb = array('key'=>$key, 'value'=>json_encode($data), 'timeSet'=>TIMESTAMP, 'timeExpire'=>$timeExpire);
		if ($this->db->getRow("select * from `{$this->table}` where `key` = '$key'", MYSQL_ASSOC)) {
			// update
			$this->db->update($dataDb, $this->table, "`key`='{$key}'");
		}else{
			// insert
			$this->db->insert($dataDb, $this->table);
		}
	}
	

	public function delete($key) {
		$this->db->query("delete from {$this->table} where `key` = '{$key}'");
	}

	public function gc() {
		$this->db->query("delete from {$this->table} where `timeExpire` < '".TIMESTAMP."'");
	}
}



// end of script