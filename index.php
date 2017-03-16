<?php
/*
 * Created on 2016-12-3
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 define('WEBSITE_ROOT', dirname(__FILE__));
 
 /**
  * tips: include,include_once和require,require_once的用法区别
  * require 是在php脚本解析时，读取出require文件；且当文件不存在时，直接停止脚本继续执行。
  * include 是在php脚本执行时，根据逻辑判断有选择性地读取include文件；且当文件不存在时，脚本会继续执行。
  * eg : if(false) { require(‘x.php’); }  if(false) { include(‘y.php’); }  这时 x.php 会被加载，而 y.php 不会被加载。
  * require_once 和 include_once 避免了脚本的重复装载导致出错。
  */
 /*
 require_once WEBSITE_ROOT.'/router/Router.php';
 require_once WEBSITE_ROOT.'/router/Dispatcher.php'; 
 
 header('Content-type: text/html; charset=utf-8');

 $rout = new Router();
 echo "11111";
 exit;
 Dispatcher::dispatch($rout);
 */
 
 include_once 'project.php';
 
 $dispatcher = Dispatcher::instance();
 $dispatcher->dispatch();
?>
