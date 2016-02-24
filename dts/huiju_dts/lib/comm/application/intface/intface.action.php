<?
/**
 * action  对像接口
 *
 * @author hzjsq@staff.pchome.net
 * @version 0.1
 */
abstract class action {

	/**
	 * 当前系统的 request 对像，保存有所有的查询数据
	 */
	public $request = null;
	/**
	 * 当前系统的 view 对像，实际上是 template 对的一个扩展
	 */
	public $view = null;

	/**
	 * 设置 request 对像
	 *
	 * @params $request request参数对像
	 * @return void
	 */
	function setRequest($request) {

		$this -> request = $request;
	}

	/**
	 * 设置 view 模板对像
	 *
	 * @params $view 模板对像
	 * @return void
	 */
	function setView($view) {

		$this -> view = $view;
	}

	/**
	 * 事件执行入口，在 application 的 dispatch 入口调用
	 *
	 * @params void
	 * @return void
	 */
	function process() {

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

}
?>