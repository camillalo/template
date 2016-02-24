<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('comments');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=comments&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = commentsMdl::getInstance()->getCommentsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('a.`id`','a.`uid`','b.`username`','a.`create_time`','a.`comments`','a.`is_show`');     
    $datas = commentsMdl::getInstance()->getCommentsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了评论列表');
    require TEMPLATE_PATH.'comments/main.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = commentsMdl::getInstance()->getComments($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        $info['create_time'] = empty($_POST['create_time']) ? '': trim(htmlspecialchars($_POST['create_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['create_time'])) errorAlert('评论时间不能为空');
        $info['comments'] = empty($_POST['comments']) ? '': trim(htmlspecialchars($_POST['comments'],ENT_QUOTES,'UTF-8'));
        if(empty($info['comments'])) errorAlert('评论内容不能为空');
        
        if(false === commentsMdl::getInstance()->updateComments($id,$info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('修改了评论',$data,$info);
        echoJs("alert('修改成功');parent.location='index.php?ctl=comments&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'comments/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = commentsMdl::getInstance()->getComments($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=comments' : $_GET['back_url'];
    if(false !== commentsMdl::getInstance()->delComments($id)) {
        logsInt::getInstance()->systemLogs('删除了评论',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

