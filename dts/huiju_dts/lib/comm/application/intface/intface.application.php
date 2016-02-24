<?
abstract class iApplication {
	
	/**
	 * 应用程序所在的根目录
	 */
	public  	$appRoot 	= '';
	
	/**
	 * 当前 action 的 request 对像，存放有当前传入的所有信息,提供对信息处理的控制
	 */
	public  	$request 	= null;
	
	/**
	 * 当前应用的 response 对像,封装了模板对像和对模板的一些操作
	 */
	public  	$response	= null;
	
	/**
	 * 当前 action 对像，提供了流程方面的操作
	 */
	public  	$action		= null;
	
	/**
	 * 当前系统的配置信息
	 */
	public  	$config		= null;
	
	/**
	 * 处理 rewrite 和 参数分解
	 */
	public  	$params		= null;
	
	/**
	 * 析构函数
	 * 
	 * @param void
	 * @return void
	 */
	public function __construct() {
	
		if (!class_exists('core')) {
			
			require_once('comm/core/class.core.php');	
		}		
		
		//$this->initialize();
	}
	
	/**
	 * 加载一指定对像，反回实例
	 * 
	 * @param  $fileName 类所在文件名
	 * @param  $className 类名
	 * @param  $parent 所继承的接口名
	 * 
	 * @return object 
	 */
	private function loadModule($fileName, $className, $parent) {

		$fileName = $this->appRoot . $fileName;	
		if (file_exists($fileName)) {
			
			require_once($fileName);
			
			if (class_exists($className)) {

				$obj = new $className;

				if (!($obj instanceof $parent)) {
					
					die("在类文件 “{$fileName}” 中定义的类 {$className},必需从接口 {$parent} 继承");	
				} else {
					
					return $obj;
				}
			} else {
				
				die("在类文件 “{$fileName}” 中没有定义类 {$className}");		
			}
		} else {
			
			die("类文件 “{$fileName}” 不存在，请先建立");
		}
	}
	
	/**
	 * 初始化 application 对像
	 * 
	 * @param void
	 * @return void
	 */
	public function initialize() {
		
		$this->appRoot = dirname($_SERVER['SCRIPT_FILENAME']) ."/";
		
		ini_set('include_path', ini_get('include_path') . _PATH_SEPARATOR . $this->appRoot);
		
		@set_magic_quotes_runtime(false);//加了个@，新版本php中废弃了此函数
		$this->config = core::Singleton('comm.application.config.config');
		
		require_once('comm/application/intface/intface.urlparse.php');
		require_once('comm/application/intface/intface.request.php');
		
		$this->params = $this->loadModule($this->config->paramsFix . '/class.params.php', 'params', urlparse);
		$this->params->initialize();
		
		$this->request = $this->params->getRequest();
		
		require_once('comm/application/intface/intface.view.php');
		$this->view =core::Singleton('comm.application.view.defaultView');

		require_once('comm/application/intface/intface.action.php');
		$fileName = sprintf('/class.act.%s.php', $this->request->getAction());
		
		$className = sprintf('act_%s', $this->request->getAction());
		
		$this->action = $this->loadModule($this->config->actionFix . $fileName, $className, action);
		$this->action->setRequest($this->request);
		$this->action->setView($this->view);
		$this->view->setConfig($this->config);
		$this->view->setRequest($this->request);
	}
	
	/**
	 * 开始前的一些操作,影响全站
	 * 
	 * @param void
	 * @return void
	 */
	public abstract function runFirst();
	
	/**
	 * 系统开始运行
	 * 
	 * @param void
	 * @return void
	 */
	public function dispatch() {
		
		$this->runFirst();
		
		$this->action->process();
		$this->view->display();
	}
	
	/**
	 * 手工指定应用所在的根目录
	 * 
	 * @param $rootPathName 应用所在的目录
	 * 
	 * @return void
	 */
	public function setRoot($rootPathName) {
		
		$rootPathName = trim($rootPathName);
		
		if ($rootPathName) {
			
			$this->appRoot = $rootPathName;	
		}
	}
	
	/**
	 * 指定当前系统运行在调试模式
	 * 
	 * @param void
	 * @return void
	 */
	public function debug() {
		
		$this->config->debug;
	}
	
	
	/**
	 * 指定当前系统处于发布模式
	 * 
	 * @param void
	 * @return void
	 */
	public function release() {
		
		$this->config->release;	
	}
	
	/**
	 * 返当前系统的 request 对像
	 * 
	 * @param void
	 * @return object
	 */
	public function getRequest() {
	
		if (is_object($this->request)) {
		
			return $this->request;	
		} else {
			
			return null;	 	
		}
	}
	
	/**
	 * 返当前系统的 response 对像
	 * 
	 * @param void
	 * @return object
	 */
	public function getResponse() {
	
		if (is_object($this->response)) {
		
			return $this->response;	
		} else {
			
			return null;	 	
		}	
	}
	
	/**
	 * 返当前系统的 action 对像
	 * 
	 * @param void
	 * @return object
	 */
	public function getAction() {
	
		if (is_object($this->action)) {
		
			return $this->action;
		} else {
			
			return null;	 	
		}	
	}
	
	
	/**
	 * 返当前系统的 configuration 对像
	 * 
	 * @param void
	 * @return object
	 */
	public function getConfig() {
	
		if (is_object($this->config)) {
		
			return $this->config;
		} else {
			
			return null;	 	
		}	
	}
	
	/**
	 * 析构
	 * 
	 * @param void
	 * @return object
	 */
	public function __destruct() {
		
		
	}
}