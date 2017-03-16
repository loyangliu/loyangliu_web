<?php


if (defined('DB_DBO_PHP'))
{
	return;
}
else
{
	define('DB_DBO_PHP', 1);
}


if (! defined ( 'DB_CHARSET' )) {
	define ( 'DB_CHARSET', 'utf8' );
}

  /**
   * database object class
   * 
   */
abstract class DBO
{
	const FETCH_ASSOC = 1; // MYSQL_ASSOC;
	const FETCH_NUM = 2; //MYSQL_NUM;
	const FETCH_BOTH = 3; //MYSQL_BOTH;
	const FETCH_DEFAULT = 1; //MYSQL_BOTH
	
	protected $dsn;  //data source name
	
	protected function __construct()
	{
	}
	

	public static function & instance($dsn)
	{
		//static $_this = null;
		
		$types = array('mysqli' => 'MySQLi', 'mysql'=>'mysql');

		libs('db' . DS . lower($dsn['type']));
		
		$classname = "DB_" . $types[lower($dsn['type'])];
		if(!class_exists($classname))
		{
			ethrow("unsupport database type: {$dsn['type']}");
		}
		$_this = new $classname;
		$_this->dsn = $dsn;
		return $_this;
	}
	

	public abstract function connect();
	public abstract function close();
	public abstract function query($sql, $limit = null);
	public abstract function update($data, $table, $where);
	public abstract function insert($data, $table);
	public abstract function getOne($sql);
	public abstract function getCol($sql, $limit = null);
	public abstract function getRow($sql, $fetchModel = self::FETCH_DEFAULT);
	public abstract function getAll($sql, $limit = null, $fetchModel = self::FETCH_DEFAULT);
	
}




// end of script