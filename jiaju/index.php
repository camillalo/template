<?php 
define('BASE_PATH',dirname(__FILE__).'/');
//获取 url绝对路径 图片显示的时候需要
define('URL',substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'], 'index.php')));
if(!file_exists(BASE_PATH.'data/install.lock')){
    header("Location: ".URL."install/index.php");
    die;
}
ini_set("display_errors","On");
//error_reporting(0);
header("Content-Type: text/html;Charset=UTF-8");
require BASE_PATH.'core/library/import.lib.php';
require BASE_PATH.'core/config.ini.php';
require BASE_PATH.'core/common.ini.php';
require BASE_PATH.'core/plugins.ini.php';
mb_internal_encoding("UTF-8");
date_default_timezone_set('Asia/Shanghai');

define('NOWTIME',$_SERVER['REQUEST_TIME']);
import::getLib('mysql');
import::getLib('filecache');
import::getLib('html');
import::getLib('mkurl');
import::getInt('area');
import::getInt('category');
$__TEMPLATE_ID = isset($__SETTING['template']) ? $__SETTING['template']:'v3.0';
define('TEMPLATE_PATH',BASE_PATH.'themes/'. $__TEMPLATE_ID .'/');
$_GET = mkUrl::getRewriteArgument();//必须要提前CTL必须要过滤/
$_GET['ctl'] = empty($_GET['ctl']) ? 'index' : preg_replace('/[^0-9a-z]/uim','', $_GET['ctl']); //一定要过滤切记
$_GET['act'] = empty($_GET['act']) ? 'main' : preg_replace('/[^0-9a-z_.\-]/uim','', $_GET['act']);

//启动敏感词防火墙
import::getInt('sensitiveWord');
sensitiveWord::getInstance()->init();

//启动防注水
import::getInt('injectionInfo');
injectionInfo::getInstance()->init();

$filename = BASE_PATH.'core/ctl/'.$_GET['ctl'].'.ctl.php';
if(file_exists($filename)){
    require $filename;
}
show404();