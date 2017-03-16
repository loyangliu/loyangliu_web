<?php

include_once 'basics.php';
include_once 'router.php';

class Dispatcher
{
    public static $_this = null;
    
    public $passargs = null;
    
	protected function __construct()
	{
		
	}

	public static function instance()
	{
		if(!Dispatcher::$_this)
		{
			Dispatcher::$_this = new Dispatcher;
		}
		
		return Dispatcher::$_this;
	}
	


	/** 
	 * get current url then dispatch
	 * 
	 * @param additionalParams 
	 * 
	 * @return 
	 */
	public function dispatch($__url = null)
	{
		$url = $__url;		
		if(!$url)
		{
			$url = $this->getUri();   
		}
		
		list($serverPath, $url) = $this->filterUri($url);
		list($named, $passargs) = $this->parseParams($url);
		
		// eg: $named=array("controller"=>"base", "action"=>"test")
		$ctrlName = lower($named['controller']);
		$action = $named['action'];
		$this->passargs = $passargs;
		$serverPath = $serverPath . DS . lower($ctrlName);
		
		return $this->__dispatchMethod($serverPath, $ctrlName, $action, $passargs);
	}
	
	/* 
	 * 获取URI
	 * 例如， 若  $_SERVER['REQUEST_URI']=/index.php/autotest/mobileplan/index?Xs=ss
	 * 经过处理后，返回应用的URI为： /autotest/mobileplan/index?Xs=ss
	 * 
	 */
	function getUri()
	{
		$url = getUri();
			
		if ($url == '/'
				|| $url == '/index.php')
		{
			$_GET['url'] = '/';
			$url = '/';
		}
		else
		{
			if (strpos($url, 'index.php') != false)
			{
				$elements = explode('/index.php', $url);
			}
			elseif(strpos($url, '/?') != false)
			{
				$elements = explode('/?', $url);
			}
			else
			{
				$elements[1] = $url;
			}

			if (!empty($elements[1]))
			{
				$url = $elements[1];
				$_GET['url'] = $elements[1];
			}
			else
			{
				$_GET['url'] = '/';
				$url = '/';
			}
		}
	
		return $url;
	}
	
	/*
	 * 对URL进行处理，得到app应用根目录及文件url
	 * 例如， getUri() 返回：/system/base/test ， 且app目录结构为 ：app/system/base/class/base.controller.php
	 * 经过处理后，返回 serverapth= xxx/app/system   ,   url=/base/test
	 *
	 */
	protected function filterUri($url)
	{
		if($url && $url[0] != '/')
		{
			$url = '/' . $url;
		}
	
		$serverPath = APP_PATH;
		$ajax = 0;
		$soap = 0;
	
		$lastPath = $serverPath;
		$lastUrl = "";
	
		while($url)
		{
			$r = null;
			$regexp = '/([^\/\?]+)/';
			if(!preg_match($regexp, $url, $r))
			{
				break;
			}
	
			$dir = $serverPath . DS . lower($r[1]);
			$filename = $dir . DS . CLASS_DIR . DS . lower($r[1]) . CONTROLLER_EXT;
	
			if(file_exists($dir))
			{
				$lastPath = $serverPath;
				$lastUrl = $url;
			}
			else
			{
				break;
			}
	
			$serverPath .= DS . lower($r[0]);
			$url = preg_replace('/\/' . lower($r[1]) . '/', '', $url);
		}
	
		$serverPath = $lastPath;
		$url = $lastUrl;
	
		return array($serverPath, $url);
	}
	
	public function parseParams($url)
	{
		$named = array();
		$passargs = array();
	
		$route = & Router::instance();
		$route->connect('/', array('controller' => 'default', 'action' => 'index'));
	
		$params = $route->parse($url);
		foreach($params as $key=>$value)
		{
			if($key != 'pass')
			{
				$named[$key] = $value;
			}
		}
	
		$passargs = $params['pass'];
		
		return array($named, $passargs);
	}



/** 
 * dispatchMethod, can only call by scake
 * eg : http://www.loyangliu.com/index.php/system/base/test?a=1
 * @param serverPath   -- /home/liuyang/workspaces/php/www.loyangliu.com/app/system/base
 * @param ctrlName  -- base 
 * @param action  -- test
 * @param passargs 
 * 
 * @return 
 */
	public function __dispatchMethod($serverPath, $ctrlName, $action, $passargs = array())
	{
		$params = array();
		if(get_magic_quotes_gpc())
		{
			if(!empty($_POST))
			{
				$params = array_merge($params, stripslashes_deep($_POST));
			}

			if(!empty($_GET))
			{
				$params = array_merge($params, stripslashes_deep($_GET));
			}

		}
		else
		{
			if(!empty($_POST))
			{
				$params = array_merge($params, $_POST);
			}

			if(!empty($_GET))
			{
				$params = array_merge($params, $_GET);
			}
		}
		
		
		if (empty($action))
		{
			$action = 'index';
		}
		

		$webroot = preg_replace('/\/index.php.*/', '', env('PHP_SELF'));
		$webroot = preg_replace('/[^\/]+\.php/i', '', $webroot);
		
		$virtualPath = str_replace(WEBROOT_PATH, '', $serverPath);
		$virtualPath = str_replace(DS, '/', $virtualPath);
		$virtualPath = $webroot . $virtualPath;
		//$virtualPath = str_replace('//', '/', $virtualPath);
		
		$GLOBALS['serverPath'] = $serverPath;
		if (empty($ctrlName))
		{
			ethrow("empty controller name");
		}
		else
		{
			$ctrlClass =camelize($ctrlName) . 'Controller';
			
			//echo "serverPath=".$serverPath.", ctrlName=".$ctrlName;exit;

			if (!loadController($serverPath, $ctrlName))
			{
				ethrow("missing controller: $ctrlClass");
			}
		}
		
		
		$controller = new $ctrlClass();	
		$classMethods = get_class_methods($controller);

		if(in_array($action, 
				$controller->builtins))
		{
			ethrow("invalid action: $action");
		}

		if (!in_array($action, $classMethods)
			&& !in_array(strtolower($action), $classMethods))
		{
			ethrow("missing action: {$controller->action}");
		}


		$indexUrl = '';
		if(defined('BASE_URL'))
		{
			$indexUrl = $webroot . '/index.php';
		}
		else
		{
			$indexUrl = $webroot;
		}
		
		$ctrlName = preg_replace('/\.ajax/', '', $ctrlName);
		$ctrlName = preg_replace('/\.soap/', '', $ctrlName);
		$controller->name = $ctrlName;
		$controller->action = $action;
		$controller->webroot = $webroot; // maybe empty
		$controller->serverPath = $serverPath;
		$controller->virtualPath = $virtualPath;
		$controller->params = & $params;
		$controller->passargs = & $passargs;
		$controller->indexUrl = $indexUrl; // xxxx/index.php
		$baseUrl = preg_replace('/.*?\/' . APP_DIR . '/', '', $virtualPath);
		$ajaxUrl = $indexUrl . '/ajax' . $baseUrl;
		$controller->ajaxUrl = $ajaxUrl;
		$baseUrl = $indexUrl . $baseUrl;
		$controller->baseUrl = $baseUrl;
		//$controller->href = $baseUrl . '/' . $action;
		$controller->href = getUri();
		$host = 'http://' . env('SERVER_NAME');
		if(env('SERVER_PORT') != 80)
		{
			$host .= ':' . env('SERVER_PORT');
		}
		$controller->hostUrl = $host;
		
		$controller->initialize();
		
		$dummy = array(0,0,0,0,0,0,0,0,0,0);
		$passargs = array_merge($passargs, $dummy);
		
		return $this->invoke($controller, $action, $passargs);
	}
	

/** 
 * invoke action-function of controller
 * 
 * @param controller 
 * @param action 
 * @param passargs 
 * 
 * @return 
 */
	protected function invoke(& $controller, $action, & $passargs)
	{
		$controller->beforeFilter();
		call_user_func_array(array(& $controller, $action), $passargs);
		$controller->afterFilter();
	}


	

	



	
	




}




// end of script
