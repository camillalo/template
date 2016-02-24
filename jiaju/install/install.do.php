<?php
if(!defined('INSTALL')) die('sorry');
$localhost=empty($_POST['localhost'])?'':trim($_POST['localhost']);
$db_name=empty($_POST['db_name'])?'':preg_replace('/[^0-9a-z_.\-]/uim','',$_POST['db_name']);
$db_user=empty($_POST['db_user'])?'':preg_replace('/[^0-9a-z_.\-]/uim','',$_POST['db_user']);
$db_password=empty($_POST['db_password'])?'':trim($_POST['db_password']);
$db_pre=empty($_POST['db_pre'])?'zx_':preg_replace('/[^0-9a-z_.\-]/uim','',$_POST['db_pre']);
if(empty($db_name)){die("数据库名称不能为空
<a href=\"javascript:history.go(-1);\">返回</a>");};
$username = empty($_POST['username'])?'':trim($_POST['username']);
$password = empty($_POST['password'])?'':trim($_POST['password']);
$password2 = empty($_POST['password2'])?'':trim($_POST['password2']);
if($password !== $password2) die("两次密码输入不一致<a href=\"javascript:history.go(-1);\">返回</a>");
$mail = empty($_POST['mail'])?'': trim($_POST['mail']);

$conn = mysql_connect($localhost,$db_user,$db_password) or die("数据库连接失败<a href=\"javascript:history.go(-1);\">返回</a>");
$is_db = null;
$dbs= mysql_query("show DATABASES",$conn);
while(($rel=mysql_fetch_assoc($dbs))!=false){
	if($rel['Database']==$db_name){$is_db=1;}
}
if(!$is_db){ mysql_query("create database {$db_name}",$conn) or die("创建数据库失败<a href=\"javascript:history.go(-1);\">返回</a>");}
mysql_select_db($db_name,$conn);
mysql_unbuffered_query("set names utf8 ",$conn);

$str = '<?php
if ( !defined ( \'BASE_PATH\') )
{
    exit ( \'Access Denied\' );
}
require BASE_PATH.\'core/setting.ini.php\';

define(\'DB_FIX\',\''.$db_pre.'\'); //数据表的前缀
define(\'SITE_KEY\' ,\''.$sitekey.'\');
//数据库配置
$__MYSQL_CFG = array(
    \'host\'          => \''.$localhost.'\',
    \'username\'      => \''.$db_user.'\',
    \'password\'      => \''.$db_password.'\',
    \'charset\'       => \'utf8\',
    \'pconnect\'      => false,
    \'dbname\'        => \''.$db_name.'\',
    \'debug\'         => false,
    \'logSql\'        => false,
    \'maxLogedSql\'   => 50
);

$_FILE_CACHE_CFG = array(
    \'cache_dir\'     => BASE_PATH.\'data/cache/\',
    \'life_time\'     => 7200,
    \'sub_dir_len\'   => 1,
    \'hash_level\'    => 1    
);
';

$sql = file_get_contents('data/table.sql');
$sql = str_replace("\r","\n",$sql);
$sql = explode(";\n",$sql);
foreach($sql as $q){
    mysql_query($q);
}

$sql2 = file_get_contents('data/data.sql');
$sql2 = str_replace("\r","\n",$sql2);
$sql2 = explode(";\n",$sql2);
foreach($sql2 as $q){
    mysql_query($q);
}
mysql_query("INSERT INTO {$db_pre}admin (username,password,realname,email,group_id,is_lock) VALUES( '".$username."', '".md5($password)."', '管理员', '".$mail."',1, 0);");
if(mysql_errno() > 0 ){
    var_dump(mysql_error());
    die;
}
file_put_contents(BASE_PATH.'core/config.ini.php', $str);
file_put_contents(BASE_PATH.'data/install.lock', 1);
require BASE_PATH.'install/template/install.html';
die;