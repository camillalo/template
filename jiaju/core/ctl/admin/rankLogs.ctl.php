<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('rankLogs');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=rankLogs&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = rankLogsMdl::getInstance()->getRankLogsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');    $col = array('a.`id`','a.`uid`','b.`realname`','c.`username`','d.`rank_name`','a.`create_time`');     
    $datas = rankLogsMdl::getInstance()->getRankLogsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了用户等级开通日志列表');
    require TEMPLATE_PATH.'rankLogs/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['admin_id'] =empty($_POST['admin_id']) ? 0: (int)$_POST['admin_id'];
        if(empty($info['admin_id'])) errorAlert('管理员不能为空');
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['rank_id'] =empty($_POST['rank_id']) ? 0: (int)$_POST['rank_id'];
        if(empty($info['rank_id'])) errorAlert('开通等级不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('开通时间不能为空');
        
        if(!rankLogsMdl::getInstance()->addRankLogs($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了用户等级日志',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=rankLogs&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'rankLogs/add.html';
    die;
}
