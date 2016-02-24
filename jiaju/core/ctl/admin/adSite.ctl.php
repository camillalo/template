<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('adSite');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=adSite&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = adSiteMdl::getInstance()->getAdSiteCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`type`','`name`');     
    $datas = adSiteMdl::getInstance()->getAdSiteList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了广告位');
    require TEMPLATE_PATH.'adSite/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名称不能为空');
        
        if(!adSiteMdl::getInstance()->addAdSite($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了广告位',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=adSite&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'adSite/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = adSiteMdl::getInstance()->getAdSite($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名称不能为空');
        
        if(false === adSiteMdl::getInstance()->updateAdSite($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了广告位',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=adSite&act=edit&id=".$id."'");
        die;
    } 
    logsInt::getInstance()->systemLogs('修改了广告位',$data,array());
    require TEMPLATE_PATH.'adSite/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = adSiteMdl::getInstance()->getAdSite($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=adSite' : $_GET['back_url'];
    if(false !== adSiteMdl::getInstance()->delAdSite($id)) {
       logsInt::getInstance()->systemLogs('删除了广告位',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

