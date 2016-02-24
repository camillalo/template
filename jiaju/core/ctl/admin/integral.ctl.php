<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('integral');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=integral&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = integralMdl::getInstance()->getIntegralCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.`id`'=>'DESC'); 
    $col = array('a.`id`','a.`uid`','b.`username`','a.`type`','a.`num`','a.`expires_t`','a.`t`');     
    $datas = integralMdl::getInstance()->getIntegralList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了积分列表');
    require TEMPLATE_PATH.'integral/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        if(empty($info['num'])) errorAlert('积分不能为空');
        $expires_t =empty($_POST['expires_t']) ? 0: (int)$_POST['expires_t'];
        if(empty($expires_t)) errorAlert('过期时间不能为空');
        
        $info['expires_t'] = NOWTIME + $expires_t * 86400;
        $info['t'] = NOWTIME;
        if(!integralMdl::getInstance()->addIntegral($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增用户积分',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=integral&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'integral/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = integralMdl::getInstance()->getIntegral($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('获得缘由不能为空');
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        if(empty($info['num'])) errorAlert('积分不能为空');
        $expires_t =empty($_POST['expires_t']) ? 0: (int)$_POST['expires_t'];
        if(empty($expires_t)) errorAlert('过期时间不能为空');
        
        $info['expires_t'] = NOWTIME + $expires_t * 86400;
        
        if(false === integralMdl::getInstance()->updateIntegral($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了用户积分',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=integral&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'integral/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = integralMdl::getInstance()->getIntegral($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=integral' : $_GET['back_url'];
    if(false !== integralMdl::getInstance()->delIntegral($id)) {
       logsInt::getInstance()->systemLogs('删除了用户积分',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

