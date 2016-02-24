<?php
/**
 * 前台用户类
 */

class dts_user {

	/**
	 * 用户表
	 * @var unknown_type
	 */
    public $tbl_user = 'tbl_user';
    public $tbl_login = 'tbl_user_login';

	/**
	 * 15天内未操作保持登陆状态
	 * @var unknown_type
	 */
	public $avail_time = '1296000';
    public $overtime   = 60;
    public $exp_date = 600;

//    //用户退出
//    public function loginout($real_session_id){
//        $db  = core::db()->getConnect('DTS');
//        $sql = sprintf("DELETE FROM %s WHERE login_session= '%s'",$this->tbl_login,$real_session_id);
//        return  $db->query($sql);
//    }
    //用户修改密码
    public function modifyUserPassword($userId,$passwd){
                $now   = @time();
		$db  = core::db()->getConnect('CAILAI');
		$sql = sprintf("UPDATE %s SET user_pass='%s' WHERE id = %s",$this->tbl_user,$passwd,$userId);              
		return $db->query($sql);
    }
    
    //分页获取借标数
    public function getBorrowList($offset,$pageSize){
            $db = core::db()->getConnect('CAILAI',true);        
           // $sql = sprintf("SELECT id ,borrow_name,borrow_duration,borrow_interest_rate,borrow_money,borrow_min,has_borrow,repayment_type,borrow_status FROM %s WHERE borrow_status IN (%s,%s,%s,%s) AND LENGTH(toubiao_telephone) <11 ORDER BY id DESC limit %s,%s ",
           //         'lzh_borrow_info',2,4,6,7,$offset,$pageSize);
   $sql = sprintf("SELECT id ,borrow_name,borrow_duration,borrow_interest_rate,borrow_money,borrow_min,has_borrow,repayment_type,borrow_status FROM %s WHERE borrow_status IN (%s,%s,%s,%s) AND LENGTH(toubiao_telephone) <11 ORDER BY borrow_money-has_borrow DESC ,id DESC limit %s,%s ",
                   'lzh_borrow_info',2,4,6,7,$offset,$pageSize);
      

            $zw = $db->query($sql);
            while($row = $db->fetchArray($zw)){
                $data[] = $row;
            }
            return $data;
    }
    
//分页获取借标数
    public function getBorrowListnew($offset,$pageSize,$where = ''){
    		if($where == ''){
    			$where = " 1=1 ";
    		} 	   	
            $db = core::db()->getConnect('CAILAI',true);        
   			$sql = sprintf("SELECT id ,borrow_name,full_time,add_time,deadline,borrow_uid,borrow_duration,borrow_interest_rate,borrow_money,borrow_min,has_borrow,repayment_type,borrow_status FROM %s WHERE 
%s AND borrow_status IN (%s,%s,%s,%s) AND LENGTH(toubiao_telephone) <11 ORDER BY borrow_money-has_borrow DESC ,id ASC limit %s,%s ",'lzh_borrow_info',$where,2,4,6,7,$offset,$pageSize);
   			$zw = $db->query($sql);
            while($row = $db->fetchArray($zw)){
                $data[] = $row;
            }
            return $data;
    }
    
    //获取借标总数
    public function getBorrowTotal(){
            $db = core::db()->getConnect('CAILAI',true);        
            $sql = sprintf("SELECT count(*) AS total FROM %s",'lzh_borrow_info');
            $zw = $db->query($sql,1);
            return $zw;
    }
    
 	//获取借标总数
    public function getBorrowTotalnew($data = ''){
    		if($data == ''){
    			$data = " 1=1 ";
    		} 	
            $db = core::db()->getConnect('CAILAI',true);        
            $sql = sprintf("SELECT count(*) AS total FROM %s where %s ",'lzh_borrow_info',$data);
            $zw = $db->query($sql,1);
            return $zw;
    }
    

        
    //添加用户首次注册信息
    public function addFirstMemberInfo($datam){
            $db = core::db()->getConnect('CAILAI');        
            $sql = sprintf("INSERT INTO %s SET uid = '%s',cell_phone = '%s'",
                    "lzh_member_info",$datam['uid'],$datam['cell_phone']);
            $result = $db->query($sql);
            return $result;
    }
    //添加注册用户
    public function addRegistUser($datar){             
            $user_name = $datar['user_name'];
            $user_pass = $datar['user_pass'];
            $tuijian_id = $datar['tuijian_id'] ;
            $db = core::db()->getConnect('TUSER');        
            $sql = sprintf("INSERT INTO %s SET user_name = '%s',user_pass = '%s',tuijian_id='%s',lingyong_quan='1'",
                    "tbl_user",$user_name,$user_pass,$tuijian_id);
            $result = $db->query($sql);
            return mysql_insert_id();     
    }
    
 	//用户反馈
    public function feedback($user_id,$content){
            $db = core::db()->getConnect('TUSER',true);
            $time = date('Y-m-d H:i:s',time());        
            $sql = sprintf("insert into %s set user_id=%s,content='%s',insert_time='%s'",'tbl_feedback',$user_id,$content,$time);
            $db->query($sql);
            return mysql_insert_id();
    }
    
	//添加领取票
    public function addlingqupiao($tuijian_id){                        
            $db = core::db()->getConnect('TUSER');        
            $sql = sprintf("update %s set lingyong_quan = lingyong_quan+1 where id = %s",
                    "tbl_user",$tuijian_id);
            $db->query($sql);     
    }
    
    //检查是否是自己的飞单
    public function check_self($user_id,$order_id){                        
            $db = core::db()->getConnect('TUSER');        
            $sql = sprintf("select count(*) as num from %s where user_id=%s and id=%s",
                    "tbl_order",$user_id,$order_id);
            return $db->query($sql,'array');     
    }
    
    //检查是否有领取票
    public function check_quan($user_id){                        
            $db = core::db()->getConnect('TUSER');        
            $sql = sprintf("select lingyong_quan from %s where id=%s",
                    "tbl_user",$user_id);
            return $db->query($sql,'array');     
    }
    
   //检查是否重复领取飞单
    public function check_chongfu($user_id,$order_id){                        
            $db = core::db()->getConnect('TUSER');        
            $sql = sprintf("select count(*) as num from %s where lingqu_user_id=%s and order_id=%s",
                    "tbl_lingqu",$user_id,$order_id);
            return $db->query($sql,'array');     
    }
    
 	//扣除领取券
    public function del_lingququan($user_id){                        
            $db = core::db()->getConnect('TUSER');        
            $sql = sprintf("update %s set lingyong_quan=lingyong_quan-1 where id=%s",
                    "tbl_user",$user_id);
            $db->query($sql);     
    }
    
 	//增加领取券使用记录
    public function lingqu_log($user_id,$order_id){                        
            $db = core::db()->getConnect('TUSER');
            $time = date('Y-m-d H:i:s',time());              
            $sql = sprintf("insert into %s set lingqu_user_id=%s,lingqu_title='%s',lingqu_time='%s',lingqu_leixing=%s",
                    "tbl_lingqu_quan",$user_id,"领取ID{$order_id}",$time,2);
            $db->query($sql);     
    }
    
    //领取飞单
	public function lingqu_feidan($user_id,$order_id){                        
            $db = core::db()->getConnect('TUSER');
            $time = date('Y-m-d H:i:s',time());         
            $sql = sprintf("insert into %s set order_id=%s,lingqu_user_id=%s,lingqu_time='%s'",
                    "tbl_lingqu",$order_id,$user_id,$time);
            return $db->query($sql);     
    }
    
	//查询用户信息
	public function user($user_id,$mobile){                        
            $db = core::db()->getConnect('TUSER');
            if(!empty($user_id))         
            	$sql = sprintf("select * from %s where id=%s","tbl_user",$user_id);
            else
            	$sql = sprintf("select * from %s where user_name=%s","tbl_user",$mobile);
            return $db->query($sql,'array');     
    }
    
    
	//领取票记录
    public function lingqulog($id,$title){                        
            $db = core::db()->getConnect('TUSER');
            $time = date('Y-m-d H:i:s',time());        
            $sql = sprintf("insert into %s set lingqu_user_id='%s',lingqu_title='%s',lingqu_time='%s',lingqu_leixing='1'",
                    "tbl_lingqu_quan",$id,$title,$time);
            $db->query($sql);     
    }
    
     //获取飞单列表
     public function get_feidan_list($page,$page_num,$user_id=""){
     	    $x = '';
     		if(!empty($user_id))
     			$x = " left join tbl_lingqu c on a.id=c.order_id ";
     	    $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("select * from %s a inner join %s b on a.id=b.order_id {$x} order by insert_time desc limit %s,%s",'tbl_order','tbl_order_house',($page-1)*$page_num,$page_num);
            $registerInfo = $db->query($sql);
	            while($row = $db->fetchArray($registerInfo)){
	            	$rows[] = $row;	            	
	            }             
            return $rows;
     }
     
	//获取飞单总数
     public function get_feidan_sum(){
     	    $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("select count(*) as num from %s",'tbl_order');
            $registerInfo = $db->query($sql,'array');         
            return $registerInfo['num'];
     }
     
 	 //获取我的领取飞单
     public function get_myfeidan($page,$page_num,$user_id=''){
     	    $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("select * from %s a left join %s b on a.order_id=b.id left join %s c on a.order_id=c.order_id where a.lingqu_user_id=%s order by b.insert_time desc limit %s,%s",'tbl_lingqu','tbl_order','tbl_order_house',$user_id,($page-1)*$page_num,$page_num);
            $registerInfo = $db->query($sql);
	            while($row = $db->fetchArray($registerInfo)){
	            	$rows[] = $row;	            	
	            }             
            return $rows;
     }
     
	 //获取我的发布飞单
     public function get_my_receive($page,$page_num,$user_id=''){
     	    $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("select * from %s a left join %s b on a.id=b.order_id where a.user_id=%s order by a.insert_time desc limit %s,%s",'tbl_order','tbl_order_house',$user_id,($page-1)*$page_num,$page_num);
            $registerInfo = $db->query($sql);
	            while($row = $db->fetchArray($registerInfo)){
	            	$rows[] = $row;	            	
	            }             
            return $rows;
     }
     
     
     
	 //获取领取券列表
     public function lingququan_list($user_id,$lingqu_type){
     	    $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("select * from %s where lingqu_leixing=%s and lingqu_user_id = %s ",'tbl_lingqu_quan',$lingqu_type,$user_id);
            $registerInfo = $db->query($sql);
            while($row = $db->fetchArray($registerInfo)){
            	$rows[] = $row;	            	
            }             
            return $rows;
     }

	//获取领取券数量
     public function lingququan_num($user_id){
     	    $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("select lingyong_quan from %s where id=%s",'tbl_user',$user_id);
            $Info = $db->query($sql,'array');            
            return $Info;
     }
    
    //检查推荐人ID是否存在
    public function fetchRegisterid($register){
            $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("SELECT count(*) as num FROM %s WHERE id='%s' LIMIT 1",'tbl_user',$register);
            $registerInfo = $db->query($sql,'array') ;
            return $registerInfo;
    }
    
	//完成飞单
    public function complete_my_feidan($user_id,$order_id){
            $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("update %s set order_status=1 where user_id=%s and order_status=0 and id=%s",'tbl_order',$user_id,$order_id);
            $db->query($sql);
            return mysql_affected_rows();
    }
    
     //获取注册验证码 
     public function fetchCode($mobile){
            $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("SELECT telcode FROM %s WHERE telephone='%s'ORDER BY id DESC LIMIT 1",'tbl_register_code',$mobile);
            $codeInfo = $db->query($sql,'array');
            return $codeInfo;
     }
       
	//获取找回密码验证码 
     public function fetchpwdCode($mobile){
            $db = core::db()->getConnect('TUSER', true);
            $sql = sprintf("SELECT vcode FROM %s WHERE telephone='%s' ORDER BY id DESC LIMIT 1",'tbl_findpwd_code',$mobile);
            $codeInfo = $db->query($sql,'array') ;
            return $codeInfo;
     }
       
     
	//更改密码
     public function changepwd($data){
            $db = core::db()->getConnect('TUSER');
            $sql = sprintf("update %s set user_pass='%s' where user_name='%s'",'tbl_user',$data['user_pass'],$data['user_name']);
            $Info = $db->query($sql) ;
            return $Info;
     }
 
    /**
	 * 增加注册短信验证码 操作表
	 * @param $string $user_name
	 * @param $string $user_pwd
	 *
	 * 返回值 user_id,user_name,session_id
	 * return boolean
	 */
       public function addCode($data){
           $db = core::db()->getConnect('TUSER');
           $mobile = $data['telephone'];
           $cur_time =  $data['cur_time'];
           $telcode = $data['telcode'];
           $sql = sprintf("insert into %s set telephone='%s',cur_time='%s',telcode='%s'",'tbl_register_code',$mobile,$cur_time,$telcode);   
           return $db->query($sql);
       }
       
/**
	 * 增加更改密码短信验证码 操作表
	 * @param $string $user_name
	 * @param $string $user_pwd
	 *
	 * 返回值 user_id,user_name,session_id
	 * return boolean
	 */
       public function addpwdCode($data){
           $db = core::db()->getConnect('TUSER');
           $mobile = $data['telephone'];
           $cur_time =  $data['cur_time'];
           $vcode = $data['vcode'];
           $sql = sprintf("insert into %s set telephone='%s',cur_time='%s',vcode='%s'",'tbl_findpwd_code',$mobile,$cur_time,$vcode);  
           return $db->query($sql);
       }
	/**
	 * 用户登陆
	 * @param $string $user_name
	 * @param $string $user_pwd
	 *
	 * 返回值 id,user_name,reg_time,real_name,session_id
	 * return boolean
	 */
	public function  login($user_name, $user_pwd) {

		$db = core::db()->getConnect('TUSER', true);
		$user_name = addslashes($user_name);
		$user_pwd  = md5($user_pwd);
		$sql = sprintf("SELECT id,user_name FROM %s WHERE (user_name='%s' or id=%s) AND `user_pass`='%s'  LIMIT 1",$this->tbl_user,$user_name,$user_name,$user_pwd);
		$user_info = $db->query($sql,'array');
		if($user_info['id']) {			
			$session_id = $this->get_session($user_info['id'],$user_info['user_name']);
			$user_info['session_id'] = $session_id;
			$this->set_login($user_info['id'],$user_info['user_name'],$session_id);
			
			//$sql = "select real_name from lzh_member_info where uid = ".$user_info['id'] ." limit 1" ;
			//$user_info['real_name'] = $db->query($sql,1);
			
			return $user_info;//id, user_name,session_id
		} else {
			return false;
		}

	}

    /**
     * 判断手机号是否已经注册
     * @param $string $user_name
     * return  boolean
     */
    public function  is_exist_cell_phone_number($cell_phone_number) {
        $db = core::db()->getConnect('TUSER', true);
        $cell_phone_number = addslashes($cell_phone_number);
        $sql = sprintf("SELECT COUNT(*) FROM %s WHERE user_name = '%s'  LIMIT 1",'tbl_user',$cell_phone_number);   
        //string '0' (length=1)
        return $db->query($sql,1) ? true : false ;
    }
    
	/**
     * 实名认证
     * @param $string $user_name
     * return  boolean
     */
    public function  authentication($user_id,$real_name,$id_card) {
        $db = core::db()->getConnect('TUSER', true);
        $cell_phone_number = addslashes($cell_phone_number);
        $sql = sprintf("update %s set real_name='%s',id_card='%s' where id='%s'",'tbl_user',
       $real_name,$id_card,$user_id);   
        return $db->query($sql,1) ? true : false ;
    }
    
	/**
     * 检查是否已通过实名认证
     * @param $string $user_name
     * return  boolean
     */
    public function  check_renzheng($user_id) {
        $db = core::db()->getConnect('TUSER', true);
        $sql = sprintf("select count(*) as num from %s where id=%s and (id_card is null or id_card='')",'tbl_user',$user_id);   
        $result = $db->query($sql,'array');
        return $result['num'];
    }
    
    


	/**
	 * 设置服务端登录情况
	 * @param unknown_type $user_id
	 * @param unknown_type $user_name
	 * @param unknown_type $session_id
	 */
	public function set_login($user_id,$user_name,$session_id) {

		if(!$user_id or !$user_name) return ;
		$db  = core::db()->getConnect('TUSER');
		$now = @time();
		$sql = sprintf("insert into %s set user_name='%s', user_id='%s', login_session='%s', update_datetime='%s'",$this->tbl_login,$user_name,$user_id,$session_id,$now);
		$db->query($sql);

	}

	
	/**
	 * 设置服务端登录情况
	 * @param unknown_type $user_id
	 * @param unknown_type $user_name
	 * @param unknown_type $session_id
	 */
	public function get_login_uid($session_id) {
		
		$db  = core::db()->getConnect('TUSER', true);
		$now = @time();
		$sql = sprintf("select user_name,user_id,update_datetime from  %s where login_session='%s' limit 1",$this->tbl_login,$session_id);
		$data = $db->query($sql,'array');
		$update_datetime = $data['update_datetime'];
		$now = @time();
		
		if($update_datetime) {
			$diff        = $now - $this->avail_time;
			if( $diff < $update_datetime ) {
				
				//ldd add
				if($now - $update_datetime > 3600){
					$this->update_login($session_id);
				}
				$d['id'] = $data['user_id'];
				$d['mobile'] = $data['user_name'];
				return $d;
			}
		}
		return false;
	}
	
	 //查询订单
     public function select_order($user_id,$order_id){
            $db = core::db()->getConnect('TUSER', true);            
            $sql = sprintf("select * from %s where id=%s and user_id=%s",'tbl_order',$order_id,$user_id);
            $res = $db->query($sql,'array');
            return $res;
     }
	
 	//增加订单
     public function add_order($user_id,$kehu_data){
            $db = core::db()->getConnect('TUSER');
            if(is_array($kehu_data)){
            	foreach($kehu_data as $k=>$v){
            		$data .= "{$k} = '{$v}',";				
            	}
            	$data = substr($data, 0,strlen($data)-1);
            }
           	$time = date('Y-m-d H:i:s',time());
            $sql = sprintf("insert into %s set user_id='%s',insert_time='%s',last_update='%s',order_status='0',xiugai_num='0', %s",'tbl_order',$user_id,$time,$time,$data);
            $db->query($sql);
            return mysql_insert_id();
     }
     
     //修改订单
     public function update_order($order_id,$kehu_data){
            $db = core::db()->getConnect('TUSER');
            if(is_array($kehu_data)){
            	foreach($kehu_data as $k=>$v){
            		$data .= "{$k} = '{$v}',";				
            	}
            	$data = substr($data, 0,strlen($data)-1);
            }
           	$time = date('Y-m-d H:i:s',time());
            $sql = sprintf("update %s set last_update='%s',xiugai_num=xiugai_num+1, %s where id=%s",'tbl_order',$time,$data,$order_id);           
            return $db->query($sql);;
     }
     
     //增加房屋信息
     public function add_house($order_id,$house_data){
            $db = core::db()->getConnect('TUSER');
            if(is_array($house_data)){
            	foreach($house_data as $k=>$v){
            		$data .= "{$k} = '{$v}',";				
            	}
            	$data = substr($data, 0,strlen($data)-1);
            }
           	$time = date('Y-m-d H:i:s',time());
            $sql = sprintf("insert into %s set order_id='%s', %s",'tbl_order_house',$order_id,$data);
            $sql = $db->query($sql);
            return $sql;
     }
     
	//修改房屋信息
     public function update_house($order_id,$house_data){
            $db = core::db()->getConnect('TUSER');
            if(is_array($house_data)){
            	foreach($house_data as $k=>$v){
            		$data .= "{$k} = '{$v}',";				
            	}
            	$data = substr($data, 0,strlen($data)-1);
            }
            $sql = sprintf("update %s set %s where order_id=%s",'tbl_order_house',$data,$order_id);
            $sql = $db->query($sql);
            return $sql;
     }
     

	/**
	 * 清除已经过期的
	 * 为了保留历史数据方便查询
	 */
	public function clear_login() {
		/*
		$now   = @time();
		$diff  = ($now - $this->avail_time);
		$db  = core::db()->getConnect('TUSER');
		$sql = sprintf("delete from %s where update_datetime < %s",$this->tbl_login,$diff);
		$db->query($sql);
		*/
	}
	
	/**
	 * 更新最后使用时间
	 * @param unknown_type $session_id
	 */
	public function update_login($session_id) {
	
		$now   = @time();
		$db  = core::db()->getConnect('DTS');
		$sql = sprintf("update %s set update_datetime='%s' where login_session = '%s' limit 1",$this->tbl_login,$now,$session_id);
		$db->query($sql);
	}


	/**
	 * 设置登录后的seesson
	 * @param unknown_type $user_id
	 * @param unknown_type $user_name
	 */
	public function get_session($user_id,$user_name) {
		$rand = rand(1,100000);
		$now  = time();
		$str  = $user_id.$user_name.$now.$rand;
		unset($rand,$now);
		return md5($str);
	}
	
	/**
	 * 根据用户名获取用户lzh_members表的信息
	 * 
	 * @param int $user_id
	 * @return array 
	 */
	public function get_user_info($user_id){
		
		$db = core::db()->getConnect('CAILAI', true);
		$cell_phone_number = addslashes($cell_phone_number);
		$sql = sprintf("SELECT * FROM `%s` WHERE id = '%s'  LIMIT 1",'lzh_members',$user_id);
		return $db->query($sql,'array');
	}
        
        //转让标,分页获取借标数 by zxm
        public function getBorrowDebtList($offset,$pageSize){
                $db = core::db()->getConnect('CAILAI',true);
                $sql=  sprintf("SELECT d.transfer_price, d.status, d.money, d.total_period, d.period, d.valid, d.id as debt_id, i.id as invest_id,i.investor_uid, i.deadline, b.id, b.borrow_name, b.borrow_interest_rate,b.borrow_status,b.borrow_type,b.borrow_duration,b.repayment_type, m.user_name FROM %s d JOIN  %s i ON d.invest_id=i.id JOIN %s b ON i.borrow_id = b.id JOIN %s m ON i.investor_uid=m.id WHERE d.status not in(3,99) limit %s,%s",'lzh_invest_detb','lzh_borrow_investor','lzh_borrow_info','lzh_members',$offset,$pageSize);    
                $zw = $db->query($sql);
                while($row = $db->fetchArray($zw)){
                    $data[] = $row;
                }
                return $data;
        }
	//转让标,获取借标总数 by zxm
        public function getBorrowDebtTotal(){
                $db = core::db()->getConnect('CAILAI',true);        
                $sql = sprintf("SELECT count(*) AS total FROM %s WHERE status NOT IN (3,99)",'lzh_invest_detb');
                $zw = $db->query($sql,1);
                return $zw;
        }
}
?>