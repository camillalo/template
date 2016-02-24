<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
if($_GET['act'] === 'main'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = empty($_POST['data']) ? errorAlert('内容不能为空'):$_POST['data'];
        foreach($data as $key =>$val){
            $data[$key] = trim($val);
        }
        makeCfg('integral',$data);
        logsInt::getInstance()->systemLogs('编辑了积分设置',$data,array());
        echoJs('alert("操作成功");parent.location="index.php?ctl=integralSetting";');
        die;
    }
    $data = import::getCfg('integral');
    logsInt::getInstance()->systemLogs('查看了积分设置');
    require TEMPLATE_PATH.'integralSetting/main.html';
    die;
}