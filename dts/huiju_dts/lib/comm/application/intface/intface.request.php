<?
/**
 * request 对像接口
 *
 * @author hzjsq@staff.pchome.net
 * @version 0.1
 */
abstract class request {
	
	/**
	 * 参数对像　
	 */
	public $params = null;
	/**
	 * 在该对像中支持的do命令数组
	 */
	public $doAttr = array();
	
	/**
	 * 设置params对像
	 *
	 * @param $obj params对像
	 * @return void
	 */
	function setParent($obj) {
		
		$this->params = $obj;
	}
	
	/**
	 * 参数处理入口
	 *
	 * @param $doParams 已经分解好的do参数
	 * @return void
	 */
	function prepare($doParams) {
		
		$this->setDo($doParams);
	}
	
	/**
	 * 处理do参数
	 *
	 * @param $dParams 已经分解好的do参数
	 * @return void
	 */
	public function setDo($doParams) {
		
		if (!in_array($doParams, $this->doAttr)) {
			
			$this->params->do = $this->params->defaultDo;
		} else {
			
			$this->params->do = $doParams;
		}
	}
    
	/**
	 * 获取该 request 对像的 action 参数
	 *
	 * @param void
	 * @return String
	 */
    public function getAction() {
    	
    	return $this->params->action;
    }
    
	/**
	 * 获取该 request 对像的 do 参数
	 *
	 * @param void
	 * @return String
	 */
    public function getDo() {
    	
    	return $this->params->do;
    }
}
?>