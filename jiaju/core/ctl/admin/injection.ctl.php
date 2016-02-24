<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('injection');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=injection&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = injectionMdl::getInstance()->getInjectionCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`name`','`ctl`','`act`','`t`','`num`');     
    $datas = injectionMdl::getInstance()->getInjectionList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了防注水设置列表');
    require TEMPLATE_PATH.'injection/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名称不能为空');
        $info['ctl'] = empty($_POST['ctl']) ? '': trim(htmlspecialchars($_POST['ctl'],ENT_QUOTES,'UTF-8'));
        if(empty($info['ctl'])) errorAlert('URL控制器地址不能为空');
        $info['act'] = empty($_POST['act']) ? '': trim(htmlspecialchars($_POST['act'],ENT_QUOTES,'UTF-8'));
        if(empty($info['act'])) errorAlert('执行的ACTION脚本不能为空');
        $info['t'] =empty($_POST['t']) ? 0: (int)$_POST['t'];
        if(empty($info['t'])) errorAlert('发布间隔秒数不能为空');
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        if(empty($info['num'])) errorAlert('最大允许请求的次数不能为空');
        
        if(!injectionMdl::getInstance()->addInjection($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了防注水设置',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=injection&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'injection/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = injectionMdl::getInstance()->getInjection($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名称不能为空');
        $info['ctl'] = empty($_POST['ctl']) ? '': trim(htmlspecialchars($_POST['ctl'],ENT_QUOTES,'UTF-8'));
        if(empty($info['ctl'])) errorAlert('URL控制器地址不能为空');
        $info['act'] = empty($_POST['act']) ? '': trim(htmlspecialchars($_POST['act'],ENT_QUOTES,'UTF-8'));
        if(empty($info['act'])) errorAlert('执行的ACTION脚本不能为空');
        $info['t'] =empty($_POST['t']) ? 0: (int)$_POST['t'];
        if(empty($info['t'])) errorAlert('发布间隔秒数不能为空');
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        if(empty($info['num'])) errorAlert('最大允许请求的次数不能为空');
        
        if(false === injectionMdl::getInstance()->updateInjection($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了防注水',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=injection&act=edit&id=".$id."'");
        die;
    } 
    logsInt::getInstance()->systemLogs('打开了防注水编辑模块');
    require TEMPLATE_PATH.'injection/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = injectionMdl::getInstance()->getInjection($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=injection' : $_GET['back_url'];
    if(false !== injectionMdl::getInstance()->delInjection($id)) {
       logsInt::getInstance()->systemLogs('删除了防注水',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

