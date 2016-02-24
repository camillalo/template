<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('buildingSiteApply');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=buildingSiteApply&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    $id = empty($_GET['id']) ?  0 : (int)$_GET['id'];
    if($id){
        $where['site_id'] = $id;
        $url .='&id='.$id;
    }
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = buildingSiteApplyMdl::getInstance()->getBuildingSiteApplyCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`name`','`phone`','`comment`');     
    $datas = buildingSiteApplyMdl::getInstance()->getBuildingSiteApplyList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了在建工地的预约申请');
    require TEMPLATE_PATH.'buildingSiteApply/main.html';
    die;
}

