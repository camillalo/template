<?
abstract class urlparse {
	
	var $paramsName = 'param';
	var $urlParams = null;
	var $do = '';
	var $action = '';
	var $defaultDo = 'home';
	var $defaultAction = 'index';
	var $pLastRegExp = '/\.(html|htm)$/is';
	var $pFirstRegExp = '/^[\/]*/is';
	var $actionPrefix = '/';
	var $doPrefix = '-';
	var $paramsPrefix = '-';
	var $config = null;
	var $resuest = null;
	var $actionList = array();
	
	public function __construct() {
		
		// $this->initialize();
	}
	
	public function initialize() {
		
		$this->config = core::Singleton( 'comm.application.config.config' );
		$this->urlParams = $_GET[$this->paramsName];
		$this->prepareParms();
		$this->parse();
	}
	
	public function parse() {
		
		$_pos = strpos( $this->urlParams, $this->actionPrefix );
		if($_pos === false) {
			
			$this->parseAction( $this->urlParams );
			$doParams = '';
		} else {
			
			$_action = substr( $this->urlParams, 0, $_pos );
			$this->parseAction( $_action );
			$doParams = substr( $this->urlParams, $_pos + 1, strlen( $this->urlParams ));
		}
		
		// 处理扩展参数
		$this->parseDo( $doParams );
	}
	
	private function parseAction($actionParams) {
		
		if(! in_array( $actionParams, $this->actionList )) {
			
			$this->action = $this->defaultAction;
		} else {
			
			$this->action = $actionParams;
			$ret = true;
		}
		
		$this->do = $this->defaultDo;
		
		return $ret;
	}
	
	private function parseDo($doParams) {
		
		$doStr = preg_replace( '/' . preg_quote( $this->doPrefix ). '(.*)$/is', '', $doParams );
		$paramsStr = preg_replace( '/^([^' . preg_quote( $this->doPrefix ). ']*)' . preg_quote( $this->doPrefix ). '/is', '', $doParams );
		//$this->extParams = explode( $this->paramsPrefix, $paramsStr );
		$this->extParams = explode( $this->paramsPrefix, $doParams );
		array_shift($this->extParams);
		$_classFileName = sprintf( "%s/class.req.%s.php", $this->config->requestFix, $this->action );
		
		$_className = sprintf( "req_%s", $this->action );
		
		if(file_exists( $_classFileName )) {
			
			require_once($_classFileName);
			if(class_exists( $_className )) {
				
				$this->resuest = new $_className();
				$this->resuest->setParent( $this );
				$this->resuest->prepare( $doStr );
				$this->resuest->extParams = $this->resuest->params->extParams;
			} else {
				
				die( "在文件 $_classFileName 中，类 $_className 没有被定义" );
			}
		} else {
			
			die( "参数类 $_classFileName 不存在" );
		}
	}
	
	public function getRequest() {
		
		return $this->resuest;
	}
	
	protected function prepareParms() {
		
		if($this->pLastRegExp)
			$this->urlParams = preg_replace( $this->pLastRegExp, '', $this->urlParams );
		if($this->pFirstRegExp)
			$this->urlParams = preg_replace( $this->pFirstRegExp, '', $this->urlParams );
		
		if(method_exists( $this, 'runFirst' )) {
			
			$this->runFirst();
		}
	}
	
	abstract function runFirst();
	
	function addAction($action) {
		
		if(is_array( $action )) {
			
			array_merge( $this->actionList, $action );
		} else {
			
			$this->actionList [] = $action;
		}
	}
	
	public function setParamsName($name) {
		
		$this->paramsName = $name;
	}
	
	public function getAction() {
		
		return $this->action;
	}
	
	public function getDo() {
		
		return $this->do;
	}
	
	public final function __get($name) {
		return $this->$name;
	}
}
?>