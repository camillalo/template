<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('systemLogs');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=systemLogs&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : trim($_GET['keyword']);
    $where  = array();
    if(!empty($_GET['keyword'])){
        $where['keyword'] = $_GET['keyword'];
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $_GET['keyword'] = htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    }
    $totalnum = systemLogsMdl::getInstance()->getSystemLogsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`username`','`title`','`raw_data`','`processed_data`','`ip`','`t`','url');     
    $datas = systemLogsMdl::getInstance()->getSystemLogsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了系统日志');
    require TEMPLATE_PATH.'systemLogs/main.html';
    die;
}
