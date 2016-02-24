<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('bookingDesign');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=bookingDesign&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = bookingDesignMdl::getInstance()->getBookingDesignCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`designer_id`','`uid`','`company_id`','`name`','`tel`','`date`','`description`','`create_time`');     
    $datas = bookingDesignMdl::getInstance()->getBookingDesignList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了预约设计列表');
    require TEMPLATE_PATH.'bookingDesign/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['designer_id'] =empty($_POST['designer_id']) ? 0: (int)$_POST['designer_id'];
        if(empty($info['designer_id'])) errorAlert('设计师ID不能为空');
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['company_id'] =empty($_POST['company_id']) ? 0: (int)$_POST['company_id'];
        if(empty($info['company_id'])) errorAlert('企业ID不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('电话不能为空');
        $info['date'] = empty($_POST['date']) ? '': trim(htmlspecialchars($_POST['date'],ENT_QUOTES,'UTF-8'));
        if(empty($info['date'])) errorAlert('预约日期不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(empty($info['description'])) errorAlert('描述不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('创建时间不能为空');
        
        if(!bookingDesignMdl::getInstance()->addBookingDesign($info)) errorAlert ('操作失败');
        
        logsInt::getInstance()->systemLogs('新增了预约设计',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=bookingDesign&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'bookingDesign/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = bookingDesignMdl::getInstance()->getBookingDesign($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['designer_id'] =empty($_POST['designer_id']) ? 0: (int)$_POST['designer_id'];
        if(empty($info['designer_id'])) errorAlert('设计师ID不能为空');
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['company_id'] =empty($_POST['company_id']) ? 0: (int)$_POST['company_id'];
        if(empty($info['company_id'])) errorAlert('企业ID不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('电话不能为空');
        $info['date'] = empty($_POST['date']) ? '': trim(htmlspecialchars($_POST['date'],ENT_QUOTES,'UTF-8'));
        if(empty($info['date'])) errorAlert('预约日期不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(empty($info['description'])) errorAlert('描述不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('创建时间不能为空');
        
        if(false === bookingDesignMdl::getInstance()->updateBookingDesign($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了预约设计',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=bookingDesign&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'bookingDesign/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = bookingDesignMdl::getInstance()->getBookingDesign($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=bookingDesign' : $_GET['back_url'];
    if(false !== bookingDesignMdl::getInstance()->delBookingDesign($id)) {
       logsInt::getInstance()->systemLogs('删除了预约设计',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

