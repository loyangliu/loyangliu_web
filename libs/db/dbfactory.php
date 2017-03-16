<?php

if (defined('DB_DBFACTORY_PHP'))
{
	return;
}
else
{
	define('DB_DBFACTORY_PHP', 1);
}


include_once 'dbo.php';

class DbFactory 
{
	private static $_this = null;
	
	public static $dbset = Array();
	
	private static function openDB()
	{
		config('database');
		$dbset = & DbFactory::$dbset;
		foreach(DATABASE_CONFIG::$databases as $key => $dbconfig)
		{
			if(!isset($dbset[$key]))
			{
				$dbset[$key] = & DBO::instance($dbconfig);
			}
		}
	}
	
	public static function instance()
	{
		if(DbFactory::$_this == null)
		{
			DbFactory::$_this = new DbFactory();
		}
		
		return DbFactory::$_this;
	}
	
	private function __construct()
	{
		DbFactory::openDB();
	}
	
	public function createDefaultDBO()
	{
		if(!isset(DbFactory::$dbset['default']))
		{
			throw new Exception("cannot create default database.");
		}
		
		return DbFactory::$dbset['default'];
	}
}