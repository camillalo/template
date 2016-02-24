<?php
/**
 * 用户资产类
 */

class member {

	/**
	 * 表
	 * @var unknown_type
	 */
    private $tbl = 'lzh_member_money';
    private $table = 'lzh_members';
    private $tablem = 'lzh_member_moneylog';
    private $tablei = 'lzh_member_info';
    private $tables = 'lzh_blacklist';
    private $tbls = 'lzh_sendsms';
    private $tbli = 'lzh_investor_detail';
    
    
     //获得用户金额
    public function getUserMoney($userId){    
        $db  = core::db()->getConnect('CAILAI',true);
        $sql = sprintf("SELECT account_money,back_money FROM %s WHERE uid = %s ",$this->tbl,$userId);    
      return  $zw = $db->query($sql,'array');
    }  
    
     //获得用户真实姓名
    public function getRealName($userId){    
        $db  = core::db()->getConnect('CAILAI',true);
        $sql = sprintf("SELECT real_name FROM %s WHERE uid = %s ",$this->tablei,$userId);    
      return  $zw = $db->query($sql,'array');
    }  
    
  //记录短信发送信息
    public function addSms($mob,$content,$res){
        $currenttime = date('Y-m-d H:i:s',time());
        //$db = core::db()->getConnect('CAILAI', true);
        $db  = core::db()->getConnect('CAILAI');
        $sql = sprintf("INSERT INTO %s (telephone,send_content,back_result,send_time) VALUES ('%s','%s','%s','%s') ",$this->tbls,$mob,addslashes($content),addslashes($res),$currenttime);
       
        $zw = $db->query($sql);
    }  
//获得黑名单
    public function getBlackList(){
        $db = core::db()->getConnect('CAILAI', true);
        $sql = sprintf("SELECT telephone FROM %s ",$this->tables);
        $zw = $db->query($sql);
         while($row = $db->fetchArray($zw)){
                $data[] = $row['telephone'];
            }
         return $data;
    }
    //在线投标
    public function getMemberMoney($userId){
        $db = core::db()->getConnect('CAILAI', true);
        $sql = sprintf("SELECT account_money,back_money,money_collect FROM %s WHERE uid = '%s' ",$this->tbl,$userId);
        $zw = $db->query($sql,'array');
        return $zw;
                
    }
    
    
    //3.14	用户我的推荐
    public function getUserRecommend($userId){
        $db = core::db()->getConnect('CAILAI', true);       
        $sql = sprintf("SELECT m.id,m.user_name AS mobile,m.reg_time AS reg_date,sum(mm.affect_money) bonus,mi.real_name FROM %s AS m LEFT JOIN %s AS mm ON (m.id=mm.target_uid) LEFT JOIN %s AS mi ON(mi.uid=m.id) WHERE (m.recommend_id ='%s' AND mm.type='13') GROUP BY  m.id",$this->table,$this->tablem,$this->tablei,$userId);
//        $sql = sprintf("SELECT m.id,m.user_name AS mobile,m.reg_time AS reg_date,sum(mm.affect_money) bonus,mi.real_name FROM %s AS m LEFT JOIN %s AS mm ON (m.id=mm.target_uid) LEFT JOIN %s AS mi ON(mi.uid=m.id) WHERE (m.recommend_id ='%s') GROUP BY  mm.target_uid",$this->table,$this->tablem,$this->tablei,$userId);
        $zw = $db->query($sql);
        $data = array();
        while($row = $db->fetchArray($zw)){
            $row['id'] = (int)$row['id'];
            $row['reg_date'] = date("Y-m-d H:i:s",$row['reg_date']);
            $row['bonus'] = (float)$row['bonus'];
            $data[] = $row;
        }
        return (array)$data;
//                
    }
    
	/**
	 * 资产信息
	 * @param id $user_id 
	 * return Array
	 */
	public function get_capital($user_id) {
		$db = core::db()->getConnect('CAILAI', true);
		$sql = sprintf("SELECT * FROM %s WHERE uid='%s'  LIMIT 1",$this->tbl,$user_id);
		$capital = $db->query($sql,'array') ;
                $data = array();
                if(empty($capital)){
                    $data['account_money'] = 0.00;
                    $data['back_money']    = 0.00;
                    $data['all_money']     = 0.00;
                    $data['money_freeze'] =  0.00;
                    $data['money_collect'] = 0.00;
                    $data['wait_capital'] = 0.00;    
                    $data['wait_interest'] = 0.00;    
                    return $data;
                }
                //$interest_collection = M('investor_detail') sum(interest) as interest  $transfer_interest_collection = M('transfer_investor_detail')
                //$benefit['interest_collection'] =  $interest_collection['interest']+ $transfer_interest_collection['interest']-$transfer_interest_collection['fee'];//dai shou ben xi 
                $sql2 = sprintf("SELECT sum(interest) as interest,sum(capital) as capital FROM %s WHERE investor_uid= %s and status in (6,7) ",$this->tbli,$user_id);
                $waitMoney = $db->query($sql2,"array");
                /**
                 *  by zxm 2015-8-28
                 * 资产总额=可用余额+待收总额+冻结总额
                 * 待收总额=待收本金+待收利息
                 */
                //资产总额
		$data['all_money'] = floatval($capital['account_money']+$capital['back_money']+$capital['money_collect']+$capital['money_freeze']);
               
		$data['account_money'] = floatval($capital['account_money']+$capital['back_money']); //可用余额
                $data['money_collect'] = (float)$capital['money_collect'];//待收总额
		$data['wait_capital'] = (float)$waitMoney["capital"];//待收本金
		$data['wait_interest'] = (float)$waitMoney["interest"];//待收利息
		$data['money_freeze'] = (float)$capital['money_freeze'];//冻结总额
		$data['back_money'] = (float)$capital['back_money'];//累计收益
		
		return $data;
	}

}
?>