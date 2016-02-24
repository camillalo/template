<?php 
header("Content-Type: text/html;Charset=UTF-8");
define('BASE_PATH',dirname(__FILE__).'/../');
ini_set("display_errors","On");
//error_reporting(0);
//如果修改了admin目录请在后面同样修改下 admin 的参数 

define('URL',substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'], 'admin/index.php')));
define('NOWTIME',$_SERVER['REQUEST_TIME']);
date_default_timezone_set('Asia/Shanghai');
require BASE_PATH.'core/library/import.lib.php';
require BASE_PATH.'core/config.ini.php';
require BASE_PATH.'core/common.ini.php';
import::getLib('mysql');
import::getLib('filecache');
import::getLib('html');
import::getInt('authManager');
import::getInt('logs');
//ini_set('session.save_path',BASE_PATH.'data/tmp/');
define('TEMPLATE_PATH',BASE_PATH.'themes/admin/');
$_GET['ctl'] = empty($_GET['ctl']) ? 'index' : preg_replace('/[^0-9a-z_.\-]/uim','', $_GET['ctl']);
session_start();
if($_GET['ctl'] !== 'login' &&  $_GET['ctl'] !== 'code' &&  $_GET['ctl'] !== 'ajax'  ){
	if( empty($_SESSION['admin'])){
		session_write_close();
		header("Location: index.php?ctl=login");
	    die;
	}
	//if($_SERVER['REQUEST_METHOD'] === 'POST') errorAlert('演示站不可以提交数据');
	//if($_GET['act'] == 'del') errorAlert('不可以删除数据！');
}
$_GET['act'] = empty($_GET['act']) ? 'main'   : preg_replace('/[^0-9a-z_.\-]/uim','', $_GET['act']);  
//不需要校验权限的地方
if($_GET['ctl'] !== 'login' && $_GET['ctl'] !== 'ajax' && $_GET['ctl'] !== 'index' && $_GET['ctl'] !== 'code'){
    //print_r($_SESSION['admin']);
    if(!authManager::getInstance()->checkAuth($_GET['ctl'].'_'.$_GET['act'])) noAccess();    
}

$filename = BASE_PATH.'core/ctl/admin/'.$_GET['ctl'].'.ctl.php';
if(file_exists($filename)){
    require $filename;
    die;
}
show404(URL.'admin/index.php?act=default');