<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
require BASE_PATH.'core/setting.ini.php';

define('DB_FIX','zx_'); //数据表的前缀
define('SITE_KEY' ,'');
//数据库配置
$__MYSQL_CFG = array(
    'host'          => '192.168.199.109',
    'username'      => 'developer',
    'password'      => 'developer',
    'charset'       => 'utf8',
    'pconnect'      => false,
    'dbname'        => 'jjzh',
    'debug'         => false,
    'logSql'        => false,
    'maxLogedSql'   => 50
);

$_FILE_CACHE_CFG = array(
    'cache_dir'     => BASE_PATH.'data/cache/',
    'life_time'     => 7200,
    'sub_dir_len'   => 1,
    'hash_level'    => 1    
);
