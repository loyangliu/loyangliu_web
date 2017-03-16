<?php


if (defined('FRAMEWORK_BASICS_PHP'))
{
	return;
}
else
{
	define('FRAMEWORK_BASICS_PHP', 1);
}


/**
 * load controller located in app directory
 *
 * @param ctlname
 * @return
 */
function loadController($serverPath, $ctrlname)
{
	$classname = camelize($ctrlname) . 'Controller';
	if(class_exists($classname))
	{
		return true;
	}

	loadAppMVC('controller');

	$filename = $serverPath . DS . CLASS_DIR . DS . lower($ctrlname) . CONTROLLER_EXT;
	if(file_exists($filename))
	{
		include_once($filename);
		return true;
	}

	return false;
}

/**
 * load model located in app directory
 *
 * @param ctlname
 * @return
 */
function loadModel($serverPath, $ctrlname)
{
	$classname = camelize($ctrlname) . 'Model';
	if(class_exists($classname))
	{
		return true;
	}

	loadAppMVC('model');

	$filename = $serverPath . DS . CLASS_DIR . DS . lower($ctrlname) . MODEL_EXT;

	if(file_exists($filename))
	{
		include_once($filename);
		return true;
	}

	return false;
}

/**
 * load view located in app directory
 *
 * call by scake
 *
 * @param ctlname
 *
 * @return
 */
function loadView($serverPath, $ctrlname)
{
	$classname = camelize($ctrlname) . 'View';
	if(class_exists($classname))
	{
		return true;
	}

	loadAppMVC('view');

	$filename = $serverPath . DS . CLASS_DIR . DS . lower($ctrlname) . VIEWER_EXT;

	if(file_exists($filename))
	{
		include_once($filename);
		return true;
	}

	return false;
}

/**
 * load app controller & model & view class located in framework directory
 *
 * @return
 */
function loadAppMVC($name)
{
	if(!class_exists(ucwords($name)))
	{
		include_once(LIBS_FRAMEWORK . DS . lower($name) . PHP_EXT);
	}

	if(!class_exists('App' . ucwords($name)))
	{
		$filename = 'app.' . lower($name) . PHP_EXT;
			
		if(file_exists(LIBS_FRAMEWORK . DS . $filename))
		{
			include_once(LIBS_FRAMEWORK . DS . $filename);
		}
	}
}

/**
 * include scake libs
 *
 * call by scake
 *
 * @return
 */
function libs()
{
	$args = func_get_args();
	foreach ($args as $arg)
	{
		$filename = LIBS . DS . lower($arg) . PHP_EXT;

		if(file_exists($filename))
		{
			include_once($filename);
		}
	}
}

/**
 * include app config
 *
 *
 * @return
 */
function config()
{
	$args = func_get_args();
	foreach($args as $arg)
	{
		$filename = APP_CONFIG_PATH . DS . lower($arg) . PHP_EXT;
			
		if(file_exists($filename))
		{
			include_once($filename);
		}
	}
}









	function __autoload($className)
	{
		$filename = LIBS . DS . lower($className) . PHP_EXT;
		if(file_exists($filename))
		{
			include_once($filename);
			return;
		}
		$filename = LIBS . DS . lower(preg_replace('/_/', '', $className)) . PHP_EXT;
		if(file_exists($filename))
		{
			include_once($filename);
		}
	}

    function applibs()
    {
        $args = func_get_args();
		foreach ($args as $arg)
		{
		    $filename = APP_LIBS_PATH . DS . lower($arg) . PHP_EXT;	
			if(file_exists($filename))
			{
				include_once($filename);
			}
		}
    }

/** 
 * include app libs
 * first find in /<controller>/class, if not, find in /app/libs
 *
 * call by app
 * 
 * @return 
 */
	function uses()
	{
		$args = func_get_args();
		foreach ($args as $arg)
		{
			if(isset($GLOBALS['serverPath'])
			   && $GLOBALS['serverPath'])
			{
				$filename = $GLOBALS['serverPath'] . DS .CLASS_DIR . DS . lower($arg) . PHP_EXT;
				if(file_exists($filename))
				{
					include_once($filename);
					return;
				}
			}
	
			$filename = APP_LIBS_PATH . DS . lower($arg) . PHP_EXT;
			
			if(file_exists($filename))
			{
				include_once($filename);
			}
		}
	}


/** 
 * lower a string
 * 
 * @param str 
 * 
 * @return 
 */
	function lower($str)
	{
		if(is_array($str))
		{
			$return = array_map('lower', $str);
			return $return;
		}
		else
		{
			return strtolower($str);
		}
	}


/** 
 * upper a string
 * 
 * @param str 
 * 
 * @return 
 */
	function upper($str)
	{
		if(is_array($str))
		{
			$return = array_map('upper', $str);
			return $return;
		}
		else
		{
			return strtoupper($str);
		}
		
	}



/** 
 * strip slashes deeply
 * 
 * @param value 
 * 
 * @return 
 */
	function stripslashes_deep($value)
	{
		if (is_array($value))
		{
			$return = array_map('stripslashes_deep', $value);
			return $return;
		}
		else
		{
			$return = stripslashes($value);
			return $return ;
		}
	}


/** 
 * get uri from environment
 * 
 * 
 * @return 
 */
	function getUri()
	{
		$uri = env('REQUEST_URI');
		return $uri;
	}


/** 
 * get env
 * 
 * @param key 
 * 
 * @return 
 */
	function env($key)
	{
		if (isset($_SERVER[$key]))
		{
			return $_SERVER[$key]; //$_SERVER包含服务器和执行环境的一些信息，不同的服务器包含的内容可能有差异。
		}
		elseif (isset($_ENV[$key]))
		{
			return $_ENV[$key];  //$_ENV存储了一些系统的环境变量,因环境不同而值不同。
		}
		elseif (getenv($key) !== false)
		{
			return getenv($key);
		}

		return null;
	}



    function timetostr($timestamp)
    {
		if($timestamp <= 0)
		{
			return "";
		}
		
		return date('Y-m-d H:i:s', $timestamp);
	}

/** 
 * throw a exception
 * 
 * @param msg 
 * @param code 
 * 
 * @return 
 */
	function ethrow($msg, $code = 0)
	{
		throw new Exception($msg, $code);
	}



/** 
 * convert camel.case.word -> CamelCaseWorld
 * 
 * @param dotWord 
 * 
 * @return 
 */
    function camelize($dotWord)
    {
		return str_replace(" ", "", ucwords(str_replace(".", " ", $dotWord)));
	}


/** 
 * convert CamelCaseWord -> camel.case.word
 * 
 * @param camelCasedWord 
 * 
 * @return 
 */
    function underdot($camelCasedWord)
    {
		return lower(preg_replace('/(?<=\\w)([A-Z])/', '.\\1', $camelCasedWord));
	}



	function backupVars(& $object)
	{
		return get_object_vars($object);
	}


	function restoreVars(& $object, & $vars)
	{
		foreach($vars as $key=>$value)
		{
			if(is_bool($key)
			   || is_numeric($key)
			   || is_string($key)
			   || is_array($key))
			{
				$object->$key = $value;
			}
		}
	}


	function escapeInfo($info) {
		if (is_array($info)) {
			foreach ($info as $key => $value) {
				$info[$key] = escapeInfo($value);
			}
		} else {
			return htmlspecialchars($info);
		}
		return $info;
	}



	function genuuid()
	{
		mt_srand((double)microtime()*10000);
		$charid = strtoupper(md5(uniqid(rand(), true)));
		$hyphen = chr(45);// "-"
		$uuid = substr($charid, 0, 8) . $hyphen
			.substr($charid, 8, 4) . $hyphen
			.substr($charid,12, 4) . $hyphen
			.substr($charid,16, 4) . $hyphen
			.substr($charid,20,12);

		return $uuid;
	}


/** 
 * build uri with params
 * 
 * @param uri 
 * @param param  such as name=aa
 * 
 * @return 
 */
    function buildUri($uri, $param, $value=null)
	{
		if(is_array($param))
		{
			foreach($param as $key=>$value)
			{
				$uri = buildUri($uri, $key, $value);
			}
			return $uri;
		}
		
		$pair = "{$param}={$value}";
		if(preg_match('/[&|?](' . $param . '=[^&]+)/i', $uri, $r))
		{
			return preg_replace('/' . $r[1] . '/', $pair, $uri);
		}
		
		if(strpos($uri, '?'))
		{
			$uri = $uri . '&' . $pair;
			$uri = preg_replace('/&&/', '&', $uri);
			return $uri;
		}
		
		return $uri . '?' . $pair;
	}






/** 
 * load plugin
 * 
 * call by app
 *
 * @param name 
 * 
 * @return 
 */
    function& plugin($name, $htmlName, & $controller)
    {
		$classname = ucwords($name) . 'Plugin';

		if(!class_exists('Plugin'))
		{
			libs('plugin');
		}
		
		if(!class_exists(ucwords($classname)))
		{
			include_once(APP_PLUGINS_PATH . DS . lower($name) . DS . lower($name) . PLUGIN_EXT);
		}
		
		if(!class_exists('Controller'))
		{
			libs('controller');
		}
		
		$plugin = new $classname($htmlName, $controller);
		$plugin->initialize();
		return $plugin;
	}
// end of script
