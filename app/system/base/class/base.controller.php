<?php
/*
 * Created on 2016-12-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 
 class BaseController extends AppController {
 	
 	public function __construct() {
 		parent::__construct();
 	}
 	
 	public function loadModule($module) {
 		
 	}
 	
 	public function test() {
 		$this->view->display('index');
 	}
 }
?>
