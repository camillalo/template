<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('quantityRoom');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=quantityRoom&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = quantityRoomMdl::getInstance()->getQuantityRoomCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.`id`'=>'DESC');  
    $col = array('a.`id`','a.`uid`','c.`username`','b.`company_name`','a.`name`','a.`tel`','a.`date`','a.`description`');     
    $datas = quantityRoomMdl::getInstance()->getQuantityRoomList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了申请量房列表');
    require TEMPLATE_PATH.'quantityRoom/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        $info['company_id'] =empty($_POST['company_id']) ? 0: (int)$_POST['company_id'];
        if(empty($info['company_id'])) errorAlert('预约企业ID不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['date'] = empty($_POST['date']) ? '': trim(htmlspecialchars($_POST['date'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        
        if(!quantityRoomMdl::getInstance()->addQuantityRoom($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了申请量房',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=quantityRoom&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'quantityRoom/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = quantityRoomMdl::getInstance()->getQuantityRoom($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        $info['company_id'] =empty($_POST['company_id']) ? 0: (int)$_POST['company_id'];
        if(empty($info['company_id'])) errorAlert('预约企业ID不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['date'] = empty($_POST['date']) ? '': trim(htmlspecialchars($_POST['date'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        
        if(false === quantityRoomMdl::getInstance()->updateQuantityRoom($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了预约量房',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=quantityRoom&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'quantityRoom/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = quantityRoomMdl::getInstance()->getQuantityRoom($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=quantityRoom' : $_GET['back_url'];
    if(false !== quantityRoomMdl::getInstance()->delQuantityRoom($id)) {
        logsInt::getInstance()->systemLogs('删除了预约量房',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

