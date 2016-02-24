<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',100);//分页大小
import::getMdl('sensitiveWord');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=sensitiveWord&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = sensitiveWordMdl::getInstance()->getSensitiveWordCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`word`');     
    $datas = sensitiveWordMdl::getInstance()->getSensitiveWordList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了敏感词列表');
    require TEMPLATE_PATH.'sensitiveWord/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['word'] = empty($_POST['word']) ? '': trim(htmlspecialchars($_POST['word'],ENT_QUOTES,'UTF-8'));
        if(empty($info['word'])) errorAlert('敏感次不能为空');
        
        if(!sensitiveWordMdl::getInstance()->addSensitiveWord($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了敏感词',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=sensitiveWord&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'sensitiveWord/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = sensitiveWordMdl::getInstance()->getSensitiveWord($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['word'] = empty($_POST['word']) ? '': trim(htmlspecialchars($_POST['word'],ENT_QUOTES,'UTF-8'));
        if(empty($info['word'])) errorAlert('敏感次不能为空');
        
        if(false === sensitiveWordMdl::getInstance()->updateSensitiveWord($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了敏感词',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=sensitiveWord&act=edit&id=".$id."'");
        die;
    } 
     logsInt::getInstance()->systemLogs('打开了敏感词编辑模块');
    require TEMPLATE_PATH.'sensitiveWord/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = sensitiveWordMdl::getInstance()->getSensitiveWord($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=sensitiveWord' : $_GET['back_url'];
    if(false !== sensitiveWordMdl::getInstance()->delSensitiveWord($id)) {
       logsInt::getInstance()->systemLogs('删除了敏感词',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

