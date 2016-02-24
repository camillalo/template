<?php
class config {
	
	public $report	=  0;
	
	public $debug	= false;
	
	public $cached	= true;
	
	public $urlDebug= false;
	
	public $mineType= 'HTML';
	
	public $urlParse='default';
	
	public $requestFix = 'request';
	
	public $actionFix  = 'action';
	
	public $responseFix= 'response';
	
	public $paramsFix  = 'params';
	
	public $viewTemlpate = 'template';
	
	public $viewTemplate_c= '/dev/shm';
	
	public $viewTemplateIden = '';
	
	public function config() {
	
		$this->viewTemplateIden = dirname($_SERVER['SCRIPT_FILENAME']);	
	}
	
	public function debug() {
		
		$this->report = (E_ALL ^ E_NOTICE);
		$this->debug = true;
		$this->urlDebug = true;
	}
	
	public function release() {
		
		$this->report = 0;
		$this->debug = false;
		$this->cached =true;
		$this->urlDebug = false;	
	}
	
	public function setMineType($mineType) {
	
		$this->mineType = $mineType;	
	}
	
	public function  template($path) {
		
		$this->viewTemlpate = $path;	
	}
	
	public function template_c($path) {
		
		$this->viewTemplate_c = $path;	
	}
	
	public function template_iden($iden) {
		
		$this->viewTemplateIden = $iden;
	}
}
?>