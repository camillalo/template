<?php 
/**
 * 权限管理
 */

class privilege {
	
	public $tbl = "`dts_account`";
	
	public function get_account_infor($account_name) {
		
		$db = core::db()->getConnect('DTS');
		$account_name = addslashes($account_name);
		$sql = sprintf("SELECT * FROM %s WHERE partner_name='%s'  LIMIT 1",$this->tbl,$account_name);
		
		return $db->query($sql,'array');
	}
}
?>