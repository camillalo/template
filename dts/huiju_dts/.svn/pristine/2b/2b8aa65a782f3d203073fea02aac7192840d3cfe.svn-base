<?php
/**
 *  将邮件加入发送队列
 */

class choose_email extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	
	public function get_response() {
		
		$object      = $this->request_arr['object']?$this->request_arr['object']:'';
		$labels       = $this->request_arr['labels']?$this->request_arr['labels']:'';
		$from       = $this->request_arr['from']?$this->request_arr['from']:'';
		
		if(empty($object) || (empty($labels) && empty($from))) {
			$this->result['code'] = 9999; 
            $this->result['msg'] = '参数错误';
            return $result = (object)array(); 
		}

		$emailIds_title = array();
		$emailIds_from = array();

		if(!empty($labels) && empty($from)){ 

			foreach ($labels as $key => $value) {
				$this->check_title($object,$value,$emailIds_title);
			}

			$emailIds = array_unique($emailIds_title);

		}else if(empty($labels) && !empty($from)){ 

			foreach ($from as $key => $value) {
				$this->check_from($object,$value,$emailIds_from);
			}

			$emailIds = array_unique($emailIds_from);

		}else if(!empty($labels) && !empty($from)){ 

			foreach ($labels as $key => $value) {
				$this->check_title($object,$value,$emailIds_title);
			}

			foreach ($from as $key => $value) {
				$this->check_from($object,$value,$emailIds_from);
			}

			$emailIds = array_intersect($emailIds_title, $emailIds_from);
			$emailIds = array_unique($emailIds);
		}	

		$array_content = array();
		foreach ($object as $key => $value) {
			if(in_array($value['emaiId'], $emailIds)){ 
				$array_content[]= $value;
			}
		}

		return $array_content;

	}

	//标题匹配
	public function check_title($object,$label,&$emailIds_title){ 
		foreach ($object as $key => $value) {
			if(preg_match("/".$label."/ism", $value['title'])){ 
				$emailIds_title[] = $value['emaiId'];
			}
		}
		return $emailIds_title;
	}

	//from匹配
	public function check_from($object,$from,&$emailIds_from){ 
		foreach ($object as $key => $value) {
			if(preg_match("/".$from."/ism", $value['from'])){ 
				$emailIds_from[] = $value['emaiId'];
			}
		}
		return $emailIds_from;
	}
}

?>