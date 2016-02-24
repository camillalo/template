<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('message');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=message&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = messageMdl::getInstance()->getMessageCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`uid`','`name`','`tel`','`title`','`content`','`create_time`');     
    $datas = messageMdl::getInstance()->getMessageList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了用户留言投诉列表');
    require TEMPLATE_PATH.'message/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['content'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
        if(empty($info['content'])) errorAlert('描述不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('创建时间不能为空');
        
        if(!messageMdl::getInstance()->addMessage($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了用户留言投诉',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=message&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'message/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = messageMdl::getInstance()->getMessage($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['content'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
        if(empty($info['content'])) errorAlert('描述不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('创建时间不能为空');
        
        if(false === messageMdl::getInstance()->updateMessage($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了用户留言投诉',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=message&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'message/edit.html';
    die;
        
}

if($_GET['act'] === 'view'){    
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = messageMdl::getInstance()->getMessage($id);    
    if(empty($data)) errorAlert ('参数出错');
    logsInt::getInstance()->systemLogs('查看了用户留言投诉详情',$data,array());
    require TEMPLATE_PATH.'message/view.html';
    die;
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = messageMdl::getInstance()->getMessage($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=message' : $_GET['back_url'];
    if(false !== messageMdl::getInstance()->delMessage($id)) {
        logsInt::getInstance()->systemLogs('删除了用户留言投诉',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

