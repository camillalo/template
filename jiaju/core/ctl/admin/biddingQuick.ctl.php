<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('biddingQuick');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=biddingQuick&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = biddingQuickMdl::getInstance()->getBiddingQuickCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`mobile`','`create_time`','`create_ip`','`is_check`');     
    $datas = biddingQuickMdl::getInstance()->getBiddingQuickList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了快捷招标');
    require TEMPLATE_PATH.'biddingQuick/main.html';
    die;
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingQuickMdl::getInstance()->getBiddingQuick($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=biddingQuick' : $_GET['back_url'];
    if(false !== biddingQuickMdl::getInstance()->delBiddingQuick($id)) {
        logsInt::getInstance()->systemLogs('删除了快捷招标',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'change'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingQuickMdl::getInstance()->getBiddingQuick($id);    
    if(empty($data)) errorAlert ('参数出错');
    $info['is_check'] = 1;
    if(false !== biddingQuickMdl::getInstance()->updateBiddingQuick($id,$info)){
        logsInt::getInstance()->systemLogs('确认了快捷招标信息',$data,$info);
        $back_url = empty($_GET['back_url']) ? 'index.php?ctl=biddingQuick' : $_GET['back_url'];
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    die;
}
