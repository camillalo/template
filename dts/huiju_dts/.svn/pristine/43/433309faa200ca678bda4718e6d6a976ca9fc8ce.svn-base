 <?
/**
 * 后台管理程序入口
 */
require_once(dirname(__FILE__) . '/intface/intface.adminAction.php');
require_once(dirname(__FILE__) . '/intface/intface.application.php');

class webAdminApplication extends iApplication {

	/**
	 * 用户信息访问类
	 */
	private $_userManager;

	/**
	 * 当前系统标识
	 */
	private $_systemFlag;

	/**
	 * 不需要权限控制的ACTION
	 */
	private $_ignoreAction = array();

	/**
	 * 设置用户管理类
	 */
	public function setUserManager($obj) {
		
		$this->_userManager = $obj;
	}

	/**
	 * 获取用户管理类
	 */
	public function getUserManager() {

		return $this->_userManager;
	}

	/**
	 * 设置系统标志
	 */
	public function setSystem($flag) {

		$this->_systemFlag = $flag;
	}

	/**
	 * 获取系统标志
	 */
	public function getSystem() {

		return $this->_systemFlag;
	}

	/**
	 * 设置不需要权限控制的action
	 */
	public function setIgnoreAction($action) {

		if (is_array($action)) {

			$this->_ignoreAction = $action;
		} else {
			$this->_ignoreAction[] = $action;
		}
	}

	/**
	 * 获取不需要权限控制的action
	 */
	public function getIgnoreAction() {

		return $this->_ignoreAction;
	}

	/**
	 * 开始前的一些操作,影响全站
	 *
	 * @param void
	 * @return void
	 */
	public function runFirst(){}

	/**
	 * 系统开始运行 (重写)
	 *
	 * @param void
	 * @return void
	 */
	public function dispatch() {
		
		$this->chkPermission();
		
		$this->runFirst();
		$this->action->setApp($this);
		$this->action->process();
		$this->view->display();
	}

	/**
	 * 检查权限
	 */
	private function chkPermission() {
		
		$action = $this->request->getAction();
		
		//要检查
		if (!in_array($action, $this->_ignoreAction)) {
			
			if (!$this->_userManager->hasSystemPerm($this->_systemFlag)) {
				
				header('location: /noperm/home.html');
				exit;
			}
		}
	}
}