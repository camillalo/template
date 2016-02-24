<?
require_once (dirname(__FILE__) . '/intface.action.php');

abstract class adminAction extends action {

	/**
	 * 应用程序对像
	 */
	private $_application = null;

	/**
	 * 设置应用程序对像
	 */
	public function setApp($app) {

		$this -> application = $app;
	}

	/**
	 * 事件执行入口，在 application 的 dispatch 入口调用
	 *
	 * @params void
	 * @return void
	 */
	function process() {

		$this -> chkPermission();

		if(method_exists($this, 'runFirst')) {

			$this -> runFirst();
		}

		$_function = sprintf("_%sAct", $this -> request -> getDo());
		if(!method_exists($this, $_function)) {

			die("请在类" . get_class($this) . " 中实现方法 $_function");
		} else {

			$this -> $_function();
		}
	}

	/**
	 * 获取指定路径的访问权限
	 */
	public function getPerm($action, $do) {

		//通过 system, action, do 等三个参数到数据库中获取进行此操作的所需的权限代码
		$sys = $this -> application -> getSystem();

		//过程省略

		return  sprintf("%s-%s-%s", $sys, $action, $do);
	}

	/**
	 * 检查权限
	 */
	private function chkPermission() {
		$action = $this -> request -> getAction();
		$do = $this -> request -> getDo();
		$perm = $this -> getPerm($action, $do);

		if(!in_array($action, $this -> application -> getIgnoreAction())) {

			if(!$this -> application -> getUserManager() -> chkActionPerm($perm)) {

				//if (method_exists($this, ''))
				header('location: /noperm/home.html');
				exit ;
			}
		}
	}

}
