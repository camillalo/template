<?php
header("Content-Type: text/html;Charset=UTF-8");
define('BASE_PATH',dirname(__FILE__).'/../');
define('INSTALL',true);
define('URL',substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'], 'install/index.php')));
if(file_exists(BASE_PATH.'data/install.lock')) die('锦尚中国提示您：已经安装过了');
$_GET['do'] =  empty($_GET['do']) ? 'index' : preg_replace('/[^0-9a-z_.\-]/uim','', $_GET['do']);
$filename = BASE_PATH.'install/'.$_GET['do'].'.do.php';
if(file_exists($filename))
{
    require $filename;
    die;
}
die('404');