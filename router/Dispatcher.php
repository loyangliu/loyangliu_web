<?php
/*
 * Created on 2016-12-4
 * 请求分发到具体controller
 */

 class Dispatcher {
 	
 	public static function dispatch(& $router) {
 		$controller = Dispatcher::loadControl($router->controller);
 		if($controller != NULL) {
 			$method = "do".ucfirst($router->action);
 			$exeout = Dispatcher::doMethod($controller, $method);
 			
 			if($exeout === false) {
 				$errmsg = "method " . $method . " not found.";
 				Dispatcher::notfound($errmsg);
 			}
 		} else {
 			$errmsg = $router->controller . " not found.";
 			Dispatcher::notfound($errmsg);
 		}
 	}
 	
 	private static function loadControl($controlfile) {
 		$controlpos = strrpos($controlfile, "/");
 		if($controlpos !== false && $controlpos !== 0) {
 			$controlerFile = substr($controlfile, $controlpos + 1);
 			$controlerClass = ucfirst($controlerFile)."Controller";
 		}
 		$controlerFilePath = WEBSITE_ROOT.$controlfile . "/class/" . $controlerFile . ".controller.php";
 		if(false !== include($controlerFilePath)) {
 			$controller = new $controlerClass();
 			return $controller;
 		} else {echo "-----".$controlerFilePath;exit(0);
 			return NULL;
 		}
 	}
 	
 	private static function notfound($errmsg) {
 		header("HTTP/1.1 404 Not Found");
 		exit("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1><p> ".$errmsg." </p></body></html>");
 	}
 	
 	private static function doMethod($controllerObj, $method) {
 		if(method_exists($controllerObj, $method)) {
 			$controllerObj->$method();
 			return true;
 		} else {
 			return false;
 		}
 	}
 }
?>
