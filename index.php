<?php
/*
 * Created on 2016-12-3
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 define('WEBSITE_ROOT', dirname(__FILE__));
 
 /**
  * tips: include,include_once��require,require_once���÷�����
  * require ����php�ű�����ʱ����ȡ��require�ļ����ҵ��ļ�������ʱ��ֱ��ֹͣ�ű�����ִ�С�
  * include ����php�ű�ִ��ʱ�������߼��ж���ѡ���Եض�ȡinclude�ļ����ҵ��ļ�������ʱ���ű������ִ�С�
  * eg : if(false) { require(��x.php��); }  if(false) { include(��y.php��); }  ��ʱ x.php �ᱻ���أ��� y.php ���ᱻ���ء�
  * require_once �� include_once �����˽ű����ظ�װ�ص��³���
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
