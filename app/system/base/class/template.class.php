<?php

class template {
	
	private $templateDir;

    public function template() {
    }
    
    public function setTemplateDir($viewdir) {
    	$this->templateDir = $viewdir;
    }
    
    public function display($file) {
    	$templateFile = $this->templateDir . "/" . $file . ".tpl.php";
    	if(!file_exists($templateFile)) {
    		$errmsg = $templateFile . " not exist.";
    		$this->errmsg($errmsg);
    	} else {
    		if(false === @include($templateFile)) {
    			$errmsg = $templateFile . " cannot be loaded.";
    			$this->errmsg($errmsg);
    		}
    	}
    }
    
    private function errmsg($errmsg) {
 		exit("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"><html><head><title>template page error</title></head><body><h1>template page error</h1><p> ".$errmsg." </p></body></html>");
 	}
}
?>