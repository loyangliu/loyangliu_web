<?php
/*
 * Created on 2016-12-5
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 require_once WEBSITE_ROOT . "app/system/public/class/template.class.php";
 
 class BaseModule {
 	public $template;
 	
 	public function BaseModule() {
 		$this->initTemplate();
 	}
 	
 	private function initTemplate() {
 		$this->template = new template();
 	}
 	
 	public function setTemplateDir($templatedir) {
 		$this->template->setTemplateDir($templatedir);
 	}
 }
?>
