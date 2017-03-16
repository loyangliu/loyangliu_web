<?php

  /**
   * @file   router.php
 * @author terisli, isd, tencent <terisli@tencent.com>
 * @date   Wed Apr 08 13:29:23 2009
 * @version 1.0
 * 
 * @brief  Router class
 * 
 * 
 */

if (defined('SCAKE_ROUTER_PHP'))
{
	return;
}
else
{
	define('SCAKE_ROUTER_PHP', 1);
}


  /** 
   * Router class
   * 
   * parse controller&action from url
   * 
   * @return 
   */
class Router
{
	protected $routes = array();
	
	protected function __construct()
	{
		
	}
	
	public static function & instance()
	{
		static $_this = null;
		if(!$_this)
		{
			$_this = new Router;
		}
		
		return $_this;
	}
	

/** 
 * register a rule 
 * 注册路由规则， 如：
 * rule : [ "/" , "/^[\/]*$/" , array() , array("controller"=>"default", "action"=>"index") ]
 * @param route 
 * @param default 
 * 
 * @return 
 */
	public function connect($route, $default = null)
	{
		$parsed = $names = array();

		$r = null;
		if (($route == '') || ($route == '/'))
		{
			$regexp = '/^[\/]*$/';
			$this->routes[] = array($route, $regexp, array(), $default);
		}
		else
		{
			$elements = array();

			foreach (explode('/', $route) as $element)
			{
				if (trim($element))
				{
					$elements[] = $element;
				}
			}

			if (!count($elements))
			{
				return false;
			}

			foreach ($elements as $element)
			{

				if (preg_match('/^:(.+)$/', $element, $r))
				{
					$parsed[] = '(?:\/([^\/]+))?';
					$names[] = $r[1];
				}
				elseif (preg_match('/^\*$/', $element, $r))
				{
					$parsed[] = '(?:\/(.*))?';
				}
				else
				{
					$parsed[] = '/' . $element;
				}
			}

			$regexp = '#^' . join('', $parsed) . '[\/]*$#';
			$this->routes[] = array($route, $regexp, $names, $default);
		}
		return $this->routes;
	}

	
/** 
 * do parse
 * 
 * @param url 
 * 
 * @return 
 */
	public function parse($url)
	{
		if (ini_get('magic_quotes_gpc') == 1)
		{
			$url = stripslashes_deep($url);
		}

		if ($url && ('/' != $url[0]))
		{
			if (!defined('SERVER_IIS'))
			{
				$url = '/' . $url;
			}
		}
		$out = array('pass'=>array());
		$r = null;
		$default_route = array('/:controller/:action/* (default)',
								'/^(?:\/(?:([a-zA-Z0-9_\\-\\.\\;\\:]+)' . 
								'(?:\\/([a-zA-Z0-9_\\-\\.\\;\\:]+)' . 
								'(?:[\\/\\?](.*))?)?))[\\/]*$/',
								array('controller', 'action'),
								array());

		//$this->connect('/bare/:controller/:action/*', array('bare' => '1'));
		$this->connect('/ajax/:controller/:action/*', array('ajax' => '1'));
		$this->connect('/soap/:controller/:action/*', array('soap' => '1'));

		if (defined('WEBSERVICES') && WEBSERVICES == 'on')
		{
			$this->connect('/rest/:controller/:action/*', array('webservices' => 'Rest'));
			$this->connect('/rss/:controller/:action/*', array('webservices' => 'Rss'));
			$this->connect('/soap/:controller/:action/*', array('webservices' => 'Soap'));
			$this->connect('/xml/:controller/:action/*', array('webservices' => 'Xml'));
			$this->connect('/xmlrpc/:controller/:action/*', array('webservices' => 'XmlRpc'));
		}
		$this->routes[] = $default_route;

		if (strpos($url, '?') !== false)
		{
			$url = substr($url, 0, strpos($url, '?'));
		}

		foreach ($this->routes as $route)
		{
			list($route, $regexp, $names, $defaults) = $route;

			if (preg_match($regexp, $url, $r))
			{
				// remove the first element, which is the url
				array_shift ($r);
				// hack, pre-fill the default route names
				foreach ($names as $name)
				{
					$out[$name] = null;
				}
				$ii=0;

				if (is_array($defaults))
				{
					foreach ($defaults as $name => $value)
					{
						if (preg_match('#[a-zA-Z_\-]#i', $name))
						{
							$out[$name] =  $this->stripEscape($value);
						}
						else
						{
							$out['pass'][] =  $this->stripEscape($value);
						}
					}
				}

				foreach ($r as $found)
				{
					// if $found is a named url element (i.e. ':action')
					if (isset($names[$ii]))
					{
						$out[$names[$ii]] = $found;
					}
					else
					{
						// unnamed elements go in as 'pass'
						$found = explode('/', $found);
						$pass = array();
						foreach ($found as $key => $value)
						{
							if ($value == "0")
							{
								$pass[$key] =  $this->stripEscape($value);
							}
							elseif ($value)
							{
								$pass[$key] =  $this->stripEscape($value);
							}
						}
						$out['pass'] = array_merge($out['pass'], $pass);
					}
					$ii++;
				}
				break;
			}
		}

		
		return $out;
	}


/** 
 * escape params
 * 
 * @param param 
 * 
 * @return 
 */
	protected function stripEscape($param)
	{
		if (!is_array($param) || empty($param))
		{
			if (is_bool($param))
			{
				return $param;
			}

			$return = preg_replace('/^[\\t ]*(?:-!)+/', '', $param);
			return $return;
		}
		foreach ($param as $key => $value)
		{
			if (is_string($value))
			{
				$return[$key] = preg_replace('/^[\\t ]*(?:-!)+/', '', $value);
			}
			else
			{
				foreach ($value as $array => $string)
				{
					$return[$key][$array] = $this->stripEscape($string);
				}
			}
		}
		return $return;
	}

}


// end of script