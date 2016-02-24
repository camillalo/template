<?php 
if(!defined('BASE_PATH')) {
	exit('Access denied');
}
$_UC_SETTING = import::getCfg('ucSetting');
define('APP_CHARSET',isset($_UC_SETTING['charset']) ? (int)$_UC_SETTING['charset'] : 0);
define('UC_CONNECT',isset($_UC_SETTING['UC_CONNECT']) ? $_UC_SETTING['UC_CONNECT'] : '');
define('UC_DBHOST', isset($_UC_SETTING['UC_DBHOST']) ? $_UC_SETTING['UC_DBHOST'] : '');
define('UC_DBUSER', isset($_UC_SETTING['UC_DBUSER']) ? $_UC_SETTING['UC_DBUSER'] : '');
define('UC_DBPW', isset($_UC_SETTING['UC_DBPW']) ? $_UC_SETTING['UC_DBPW'] : '');
define('UC_DBNAME', isset($_UC_SETTING['UC_DBNAME']) ? $_UC_SETTING['UC_DBNAME'] : '');
define('UC_DBCHARSET', isset($_UC_SETTING['UC_DBCHARSET']) ? $_UC_SETTING['UC_DBCHARSET'] : '');
define('UC_DBTABLEPRE', isset($_UC_SETTING['UC_DBTABLEPRE']) ? $_UC_SETTING['UC_DBTABLEPRE'] : '');
define('UC_DBCONNECT', isset($_UC_SETTING['UC_DBCONNECT']) ? $_UC_SETTING['UC_DBCONNECT'] : '');
define('UC_KEY', isset($_UC_SETTING['UC_KEY']) ? $_UC_SETTING['UC_KEY'] : '');
define('UC_API', isset($_UC_SETTING['UC_API']) ? $_UC_SETTING['UC_API'] : '');
define('UC_CHARSET', isset($_UC_SETTING['UC_CHARSET']) ? $_UC_SETTING['UC_CHARSET'] : '');
define('UC_IP', isset($_UC_SETTING['UC_IP']) ? $_UC_SETTING['UC_IP'] : '');
define('UC_APPID', isset($_UC_SETTING['UC_APPID']) ? $_UC_SETTING['UC_APPID'] : '');
define('UC_PPP', isset($_UC_SETTING['UC_PPP']) ? $_UC_SETTING['UC_PPP'] : '');                                                                                                                