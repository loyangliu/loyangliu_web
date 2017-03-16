<?php
/*
 * Created on 2016-12-3
 * �������������ת����controller����
 */
 
 class Router {
 	public $controller;
 	public $action;
 	
 	public function __construct() {
 		$this->parseRequest();
 	}
 	
 	private function parseRequest() {
 		$requestURI = $_SERVER["REQUEST_URI"]; // eg:/index.php/a/b/c
 		$indexpage = "index.php";
 		$indexpos = stripos($requestURI, $indexpage);
 		$getindex = stripos($requestURI, "?");
 		if($indexpos !== false) { //������������Ĳ���
 			$path = substr($requestURI, $indexpos + strlen($indexpage));
 			if($getindex !== false) {
 				$pathlen = $getindex - ($indexpos + strlen($indexpage));
 				$path = substr($path, 0, $pathlen);
 				if($path[strlen($path) - 1] == '/') {
 					$path = substr($path, 0, -1);
 				}
 			}
 			
 			// װ��controller�� path : /a/b/c
 			$controlpos = strrpos($path, "/");
 			if($controlpos == 0) { // eg: ֻ��controller��û��action�� �磺/control
 				$this->controller = "/app/system/index";
 				$this->action = "default";
 			} else {
 				$this->controller = substr($path, 0, $controlpos);
 				$this->action = substr($path, $controlpos + 1);
 			}
 		} else { //Ĭ����ҳ��ʾ
 			$this->controller = "/app/system/index";
 			$this->action = "default";
 		}
 	}
 	
 }
?>
