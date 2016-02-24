<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();

if($_GET['act'] === 'main'){
    $datas  = import::getCfg('seo');
    $seo = import::getCfg('seomain');
    
    logsInt::getInstance()->systemLogs('查看了SEO配置');
    require TEMPLATE_PATH.'seo/main.html';
    die;
}

if($_GET['act'] === 'save'){
    $data = empty($_POST['data']) ? array() : $_POST['data'];
    $arr = array();
    foreach($data as $key=>$val){
        foreach($val as $k=>$v){
            $arr[$key][$k] = htmlspecialchars($v,ENT_QUOTES,'UTF-8');
        }
    }
    makeCfg('seomain', $arr);
    errorAlert('操作成功');
    die;
}