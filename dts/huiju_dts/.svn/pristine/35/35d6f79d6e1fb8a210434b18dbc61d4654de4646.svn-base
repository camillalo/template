<?php 

header("Content-type: text/html; charset=utf-8");
require_once(dirname(__FILE__).'/header.php');
core::Singleton('comm.remote.remote');
//phpinfo();
//借款合同
$data = array( 
    'sname'=>'borrow_pact',
    'bid'=>'1210'
 );
remote::$open_debug=1;
$result=remote::send($data);
var_dump($result);
exit;
//phpinfo();
//exit();
//调用dts接口来登陆
//
//$data = array(
//		'sname' => 'user.login',//系统参数，调用接口
//		'mobile' => '13511113333',//应用查参数，快递品牌
//		'passwd'    => '123456789',//应用查参数，快递单号
//);
//remote::$open_debug=1;
//$result = remote::send($data);
//print_r($result);
//print_r(json_decode($result,true));unset($result);//这里可以得到uid
//$session_id = remote::$session_id;//获取session_id
//echo $session_id;
////print_r(remote::$debug);
////print_r($result);
////echo "\n\n";
//echo "<hr>";
//exit;
////

$data = array(
		'sname' => 'user.findpwd',//系统参数，调用接口
		'mobile' => '13817524337',//应用查参数，快递品牌
);
remote::$open_debug=1;  
$result = remote::send($data);
print_R($result);
exit;

////调用dts接口
//$data = array(
//		'sname' => 'user.reg',//系统参数，调用接口
//		'mobile' => '13100030002',
//                'passwd' =>"111111",
//                'recommended' =>'18521362829',
//);
//
//remote::$open_debug=1;
//$result = remote::send($data);print_r($result);
//$result = json_decode($result,true);
//
//var_dump($result);
//print_r($result);//打印结果，body属性返回格式为json格式
////print_r(remote::$debug);
//echo "\n\n";
//exit;


//
//$data = array(
//		'sname' => 'user.recommend',//系统参数，调用接口
//		'mobile' => '18521362829',//应用查参数，快递品牌
//                'passwd' => '12345',
//                'telcode' => '123456',
//                'register' => '13800010002',
//);
//remote::$open_debug=1;
////$header[] = 'Cookie:session_id=c956017e400930235cdbc0b2648d205cf';
//$result = remote::send($data);
//print_r($result);//打印结果，body属性返回格式为json格式
////print_r(remote::$debug);
//echo "\n\n";
//exit;

//$a = '{"code":0,"msg":"\u8c03\u7528\u6210\u529f","data":{"bname":"ss","interest_rate":12,"ratio":22,"borrow_money":100000,"borrow_duration":6,"repayment_type":"\u6bcf\u6708\u8fd8\u671f\u5230\u671f\u8fd8\u672c","add_datetime":"2015-05-11 17:41:12","borrow_min":100}}';
// print_r(json_decode($a,true));exit;


//
$data = array(
		'sname' => 'withdrawals.get',//系统参数，调用接口	
//                'page_size' => 10,
//                    'page_num' => 0,
////                     'mobile' => '18521362829',//应用查参数，快递品牌
                     'amount' => 11295
);
//remote::$open_debug=1;100800.00
//12152.00
$header[] = 'Cookie:session_id=c095ca2d9929c7094837249b4e4f313c2';
$result = remote::send($data,'',$header);
print_r($result);
 $res = json_decode($result,true);//打印结果，body属性返回格式为json格式
 print_r($res);exit;
//// exit;
// 
//file_put_contents('/tmp/user1',date('m-d H:i:s')." back : ".print_r($res,true)."\n",FILE_APPEND);
if($res['code']===0){
   
	autoRedirect($res['data']);
}else{
	//print_R($res);
}
echo "\n\n";
exit;


function autoRedirect($reqData){//echo "<pre>";print_r($reqData);echo "</pre>";exit;
	$tmp = array();
	$html= <<<HTML
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<head><body onload="document.getElementById('autoRedirectForm').submit();">
<div class="margin:10px;font-size:14px;">正在跳转...</div>
<form id="autoRedirectForm" method="POST" action="http://mertest.chinapnr.com/muser/publicRequests">
HTML;
	foreach($reqData as $key => $value){
		$html.='<input type="hidden" value=\''.$value.'\' name="'.$key.'" />';
		$tmp[$key] = $value;
	}
	$html.="</form>";
	$html.="</body></html>";
	//file_put_contents('/tmp/autoRedirect',date('m-d H:i:s')."autoRedirect ".print_r($autoRedirect,true)."\n",FILE_APPEND);
	//file_put_contents('/tmp/autoRedirect',date('m-d H:i:s')."autoRedirect ".print_r($reqData,true)."\n",FILE_APPEND);
	print $html;
	exit;
}








//我的资产（需要登陆才可以）
$data = array(
		'sname' => 'member.capital',//系统参数，调用接口
);
$header[] = 'Cookie:session_id=c14e16e841390bf4be501b18b3ac56ce6';
$result = remote::send($data,'',$header);
print_R(json_decode($result));//打印结果，body属性返回格式为json格式
//print_r(remote::$debug);
echo "\n\n";
exit;


?>