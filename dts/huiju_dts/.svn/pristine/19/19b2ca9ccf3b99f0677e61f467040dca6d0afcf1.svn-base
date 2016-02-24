<?php
/**
 * 通用类
 */

class comm {

	//---------------------------------------------------------邮件功能开始-------------------------------------------------
    //邮件插入队列
    public function add_email($title,$content,$fujian_json,$server,$server_port,$ssl_port,$from_name){
        $time   = date('Y-m-d H:i:s',time());
		$db  = core::db()->getConnect('DTS',true);
		$sql = sprintf("insert into %s set title='%s',content='%s',add_time='%s',fujian='%s',server='%s',server_port='%s',ssl_port='%s',from_name='%s'",
'send_email',$title,$content,$time,$fujian_json,$server,$server_port,$ssl_port,$from_name);              
		$db->query($sql);
		return $db->insertId();
    }
    
	//插入邮件信息表
	public function add_email_msg($id,$send_to){
        $db  = core::db()->getConnect('DTS',true);
		$sql = sprintf("insert into %s set email_id=%s,send_to='%s'",'email_msg',$id,$send_to);              
		$db->query($sql);
	}
    
	//获取邮件待发队列
    public function get_email(){
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("select * from %s a inner join %s b on a.id=b.email_id where b.status='0'",'send_email','email_msg');              
		$data = $db->query($sql);
     	while($row = $db->fetchArray($data)){
            $result[] = $row;
        }
		return $result;
    }
    
    //发送成功进行记录
 	public function success_email($id,$from){
 		$time   = date('Y-m-d H:i:s',time());
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("update %s set `from`='%s',status='1',success_time='%s' where id=%s",'email_msg',$from,$time,$id);        
		$db->query($sql);
    }

    //查看数据表中是否已经存在
    public function check_title($title){
		$db  = core::db()->getConnect('DTS');
		$sql = "select * from email_content where title like '%$title%'";           
		$data = $db->query($sql);
     	while($row = $db->fetchArray($data)){
            $result[] = $row;
        }
		return $result;
    }

    //邮件内容入库
    public function insert_data($title,$content){
        $ip = $_SERVER['REMOTE_ADDR'];
		$time = date("Y-m-d H:i:s");
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("insert into %s set title='%s',content='%s',ip='%s',time='%s'",'email_content',$title,$content,$ip,$time);              
		$result = $db->query($sql);
		return $result;
    }
    
 	//邮件流水记录before
    public function email_log($email_id,$from){
		$time = date("Y-m-d H:i:s");
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("insert into %s set email_id='%s',`from`='%s',add_time='%s'",'email_log',$email_id,$from,$time);           
		$db->query($sql);
		return $db->insertId();
    }
    
	//邮件流水记录after
    public function email_log2($log_id,$state){
		$time = date("Y-m-d H:i:s");
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("update %s set state='%s',update_time='%s' where id='%s'",'email_log',$state,$time,$log_id);          
		$db->query($sql);
    }
    
    
    
    
    
    //---------------------------------------------------------邮件功能结束-------------------------------------------------
    
    //---------------------------------------------------------百度地图功能开始----------------------------------------------
    
	//当前经纬度入库
    public function add_latlng($member_id,$lat_lng,$ip,$distr){
		$time = date("Y-m-d H:i:s");
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("insert into %s set member_id='%s',latlng='%s',ip='%s',updatetime='%s',distr='%s'",'baidu_map',$member_id,$lat_lng,$ip,$time,$distr);          
		$db->query($sql);
    }
    
    
	//读取多人经纬度列表
    public function get_latlng(){
		$db  = core::db()->getConnect('DTS');
		$sql = "select * from baidu_map where updatetime in (select max(updatetime) from baidu_map group by member_id)";           
		$data = $db->query($sql);
     	while($row = $db->fetchArray($data)){
            $result[] = $row;
        }
		return $result;
    }
    
    
    
     //---------------------------------------------------------百度地图功能结束----------------------------------------------
    
    
}
?>