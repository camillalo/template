<?php
/**
 *  百度地图多人线路数据接口
 */

class map_line extends api_comm  {

	/**
	 * 设置响应的消息体  返回值必须是json
	 * 
	 */
	private $final = "121.451833,31.228585";//终点位置
	
	public function get_response() {
		require_once (dirname(__FILE__).'/../../comm/baidu_map/class.towPointDistance.php');
		
		$lat_lng      = $this->request_arr['lat_lng']?$this->request_arr['lat_lng']:'';//经纬度
		$ip      = $this->request_arr['ip']?$this->request_arr['ip']:'';//ip地址
		$member_id      = $this->request_arr['member_id']?$this->request_arr['member_id']:'';//用户id（暂时使用传值的方式，以后需要改成session头信息判断用户）
		
		if(empty($lat_lng)||empty($ip)||empty($member_id)) {
			$this->result['code'] = 9999; 
            $this->result['msg'] = '参数错误';
            return $result = (object)array(); 
		}
		
		$latlng = explode(",",$lat_lng);
		
		//错误经纬度
		if($latlng[0]=='4.9E-324'||$latlng[1]=='4.9E-324'){
			$this->result['code'] = 400; 
            $this->result['msg'] = '经纬度错误';
            return $result = (object)array(); 
		}
		$final_location = explode(",",$this->final);
		
		
		//计算当前点离终点的距离
		$distance = new towPointDistance($latlng[0],$latlng[1],$final_location[0],$final_location[1]);
   		$distr =  $distance->Handle();
		
		//当前经纬度入库
		$this->add_latlng($member_id,$lat_lng,$ip,$distr);
		
		$result = $this->get_latlng();
		if(is_array($result)){
			foreach($result as $k=>$v){
				$result[$k]['final'] = $this->final;
				
			}
		}
		
		
		
		return $result;
	}
	
	//当前经纬度入库
	private function add_latlng($member_id,$lat_lng,$ip,$distr){
         $comm = core::Singleton('user.comm');
		 $comm->add_latlng($member_id,$lat_lng,$ip,$distr);unset($comm);
	}
	
	//读取多人经纬度列表
	private function get_latlng(){
         $comm = core::Singleton('user.comm');
		 $result = $comm->get_latlng();unset($comm);
         return $result;
	}
	

	



}

?>