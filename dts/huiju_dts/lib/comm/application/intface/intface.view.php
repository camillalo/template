<?php
require_once('comm/template/class.template.php');

abstract class view extends template {
	
	private $config = null;
	private $templateFileName = null;
	private $request = null;
	
	private function getTemplateFileName() {
		
		if ($this->templateFileName) {

			return $this->templateFileName;
		} else {
			
			return sprintf("%s/%s.html", $this->request->getAction(), $this->request->getDo());	
		}
	}
	
	public function display($file = null) {
		
		if (!$file) {

			$file = $this->getTemplateFileName();
		}
		
		parent::display($file);
	}
	
	public function fetch($file = null,  $display = false, $build = false, $cache_id = null)  {
		
		if (!$file) {

			$file = $this->getTemplateFileName();
		}
		
		if (method_exists($this, 'runFirst')) {

			$this->runFirst();
		}
		
		$this->readConfigure();

		return parent::fetch($file,  $display, $build, $cache_id);		
	}
	
	public function setConfig($obj) {
		
		$this->config = $obj;	
	}
	
	public function setRequest($obj) {
		
		$this->request = $obj;	
	}
	
	public function setTemplateName($fileName) {
		
		$this->templateFileName = $fileName;
	}
	
	private function readConfigure() {
		
		$this->template_dir = $this->config->viewTemlpate;
		$this->compile_dir = $this->config->viewTemplate_c;
		$this->template_iden = $this->config->viewTemplateIden;	
	}
}