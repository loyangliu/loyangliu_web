<?php
// kiko done
if (defined ( 'CONFIG_DATABASE_PHP' )) {
	return;
} else {
	define ( 'CONFIG_DATABASE_PHP', 1 );
}
class DATABASE_CONFIG {
	public static $databases = array (
		// default(function test) database
		'default' => array (
			'type' => 'mysqli',
			'host' => '192.168.111.128',
			'user' => 'root',
			'psw' => 'liuyang',
			'database' => 'db_att_plat' 
		),
		// autotest database
		'autotest' => array (
			'type' => 'mysqli',
			'host' => '192.168.111.128',
			'user' => 'root',
			'psw' => 'liuyang',
			'database' => 'db_att_auto' 
		),
		// execute log database
		'autolog' => array (
			'type' => 'mysqli',
			'host' => '192.168.111.128',
			'user' => 'root',
			'psw' => 'liuyang',
			'database' => 'db_att_log'
		),
		'autopath' => array (
			'type' => 'mysqli',
			'host' => '192.168.111.128',
			'user' => 'root',
			'psw' => 'liuyang',
			'database' => 'db_att_path'
		),
		// execute log database
		'mlf' => array (
			'type' => 'mysqli',
			'host' => '192.168.111.128',
			'user' => 'root',
			'psw' => 'liuyang',
			'database' => 'db_att_mlf'
		),
		'rcmd' => array (
			'type' => 'mysqli',
			'host' => '192.168.111.128',
			'user' => 'root',
			'psw' => 'liuyang',
			'database' => 'db_mlf_rcmd'
		),
			
		// execute log database
		'atmConfigCenter' => array (
				'type' => 'mysqli',
				'host' => '192.168.111.128',
				'user' => 'root',
				'psw' => 'liuyang',
				'database' => 'dbCommConfigCenter'
		),
	);
}