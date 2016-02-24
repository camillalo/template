<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('askAdded');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=askAdded&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = askAddedMdl::getInstance()->getAskAddedCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`ask_id`','`added`');     
    $datas = askAddedMdl::getInstance()->getAskAddedList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了问题补充列表');
    require TEMPLATE_PATH.'askAdded/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['ask_id'] =empty($_POST['ask_id']) ? 0: (int)$_POST['ask_id'];
        if(empty($info['ask_id'])) errorAlert('问题ID不能为空');
        $info['added'] = empty($_POST['added']) ? '': trim(htmlspecialchars($_POST['added'],ENT_QUOTES,'UTF-8'));
        if(empty($info['added'])) errorAlert('问题补充不能为空');
        
        if(!askAddedMdl::getInstance()->addAskAdded($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了问题补充',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=askAdded&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'askAdded/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askAddedMdl::getInstance()->getAskAdded($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['ask_id'] =empty($_POST['ask_id']) ? 0: (int)$_POST['ask_id'];
        if(empty($info['ask_id'])) errorAlert('问题ID不能为空');
        $info['added'] = empty($_POST['added']) ? '': trim(htmlspecialchars($_POST['added'],ENT_QUOTES,'UTF-8'));
        if(empty($info['added'])) errorAlert('问题补充不能为空');
        
        if(false === askAddedMdl::getInstance()->updateAskAdded($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了问题补充',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=askAdded&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'askAdded/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askAddedMdl::getInstance()->getAskAdded($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=askAdded' : $_GET['back_url'];
    if(false !== askAddedMdl::getInstance()->delAskAdded($id)) {
       logsInt::getInstance()->systemLogs('删除了问题补充',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

