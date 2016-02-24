<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('buildingSiteStatus');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=buildingSiteStatus&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $_GET['site_id'] = empty($_GET['site_id']) ?  0 : (int)$_GET['site_id'];
    if(!empty($_GET['site_id'])){
        $url.='&site_id='.  $_GET['site_id'];
        $where['site_id'] = $_GET['site_id'];
        
    }
    
    $totalnum = buildingSiteStatusMdl::getInstance()->getBuildingSiteStatusCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`site_id`','`status`');     
    $datas = buildingSiteStatusMdl::getInstance()->getBuildingSiteStatusList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了工地流程列表');
    require TEMPLATE_PATH.'buildingSiteStatus/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['site_id'] =empty($_POST['site_id']) ? 0: (int)$_POST['site_id'];
        if(empty($info['site_id'])) errorAlert('工地ID不能为空');
        $info['status'] =empty($_POST['status']) ? 0: (int)$_POST['status'];
        if(empty($info['status'])) errorAlert('工地流程不能为空');
        $info['content'] = empty($_POST['content']) ? '': getValue($_POST['content']);
        if(empty($info['content'])) errorAlert('流程日记不能为空');
        
        if(!buildingSiteStatusMdl::getInstance()->addBuildingSiteStatus($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了工地流程',$info,array());    
        echoJs("alert('操作成功');parent.location='index.php?ctl=buildingSiteStatus&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'buildingSiteStatus/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = buildingSiteStatusMdl::getInstance()->getBuildingSiteStatus($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['site_id'] =empty($_POST['site_id']) ? 0: (int)$_POST['site_id'];
        if(empty($info['site_id'])) errorAlert('工地ID不能为空');
        $info['status'] =empty($_POST['status']) ? 0: (int)$_POST['status'];
        if(empty($info['status'])) errorAlert('工地流程不能为空');
        $info['content'] = empty($_POST['content']) ? '': getValue($_POST['content']);
        if(empty($info['content'])) errorAlert('流程日记不能为空');
        
        if(false === buildingSiteStatusMdl::getInstance()->updateBuildingSiteStatus($id,$info)) errorAlert ('操作失败');

        logsInt::getInstance()->systemLogs('修改了工地流程',$data,$info);   
        echoJs("alert('操作成功');parent.location='index.php?ctl=buildingSiteStatus&act=edit&id=".$id."'");
        die;
    } 
     logsInt::getInstance()->systemLogs('打开了工地流程编辑模块');
    require TEMPLATE_PATH.'buildingSiteStatus/edit.html';
    die;
        
}

if($_GET['act'] === 'view'){    
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = buildingSiteStatusMdl::getInstance()->getBuildingSiteStatus($id);    
    if(empty($data)) errorAlert ('参数出错');
     logsInt::getInstance()->systemLogs('查看了工地流程详情',$data,array());
    require TEMPLATE_PATH.'buildingSiteStatus/view.html';
    die;
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = buildingSiteStatusMdl::getInstance()->getBuildingSiteStatus($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=buildingSiteStatus' : $_GET['back_url'];
    if(false !== buildingSiteStatusMdl::getInstance()->delBuildingSiteStatus($id)) {
 
        logsInt::getInstance()->systemLogs('删除了工地流程',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

