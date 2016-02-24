<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('security');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=security&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $_GET['uid'] = empty($_GET['uid']) ? 0  : (int)$_GET['uid'];
    if(!empty($_GET['uid'])){
        $url.='&uid='.$_GET['uid'];
        $where['uid'] = $_GET['uid'];
    }
    $totalnum = securityMdl::getInstance()->getSecurityCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.uid'=>'DESC');   
    $col = array('a.`uid`','a.`money1`','a.`money2`','a.`special`','a.`after_sales`','b.`company_name`','c.`username`');     
    $datas = securityMdl::getInstance()->getSecurityList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
     logsInt::getInstance()->systemLogs('查看了公司保障列表');
    require TEMPLATE_PATH.'security/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('公司ID不能为空');
        $info['money1'] =empty($_POST['money1']) ? 0: (int)$_POST['money1'];
        $info['money2'] =empty($_POST['money2']) ? 0: (int)$_POST['money2'];
        $info['special'] = empty($_POST['special']) ? '': trim(htmlspecialchars($_POST['special'],ENT_QUOTES,'UTF-8'));
        if(empty($info['special'])) errorAlert('特殊服务不能为空');
        $info['after_sales'] = empty($_POST['after_sales']) ? '': trim(htmlspecialchars($_POST['after_sales'],ENT_QUOTES,'UTF-8'));
        if(empty($info['after_sales'])) errorAlert('售后服务不能为空');
        
        if(!securityMdl::getInstance()->addSecurity($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了公司保障',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=security&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'security/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
    $data = securityMdl::getInstance()->getSecurity($uid);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('公司ID不能为空');
        $info['money1'] =empty($_POST['money1']) ? 0: (int)$_POST['money1'];
        $info['money2'] =empty($_POST['money2']) ? 0: (int)$_POST['money2'];
        $info['special'] = empty($_POST['special']) ? '': trim(htmlspecialchars($_POST['special'],ENT_QUOTES,'UTF-8'));
        if(empty($info['special'])) errorAlert('特殊服务不能为空');
        $info['after_sales'] = empty($_POST['after_sales']) ? '': trim(htmlspecialchars($_POST['after_sales'],ENT_QUOTES,'UTF-8'));
        if(empty($info['after_sales'])) errorAlert('售后服务不能为空');
        
        if(false === securityMdl::getInstance()->updateSecurity($uid,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了公司保障',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=security&act=edit&uid=".$uid."'");
        die;
    } 
    logsInt::getInstance()->systemLogs('打开了公司保障的编辑页面');
    require TEMPLATE_PATH.'security/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
    $data = securityMdl::getInstance()->getSecurity($uid);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=security' : $_GET['back_url'];
    if(false !== securityMdl::getInstance()->delSecurity($uid)) {
        logsInt::getInstance()->systemLogs('删除了公司保障',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

