<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('askAnswer');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=askAnswer&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $id = empty($_GET['id']) ? 0 :(int)$_GET['id'];
    if(!empty($id)){
        $url.='&id='. $id;
        $where['ask_id'] = $id;
    }
    $totalnum = askAnswerMdl::getInstance()->getAskAnswerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');   
    $col = array('a.`id`','b.`username`','a.`uid`','a.`ask_id`','a.`content`','a.`create_time`','a.`ip`');     
    $datas = askAnswerMdl::getInstance()->getAskAnswerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了问答回答列表');
    require TEMPLATE_PATH.'askAnswer/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['ask_id'] =empty($_POST['ask_id']) ? 0: (int)$_POST['ask_id'];
        if(empty($info['ask_id'])) errorAlert('问题ID不能为空');
        $info['content'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
        if(empty($info['content'])) errorAlert('回答内容不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('发布时间不能为空');
        $info['ip'] = empty($_POST['ip']) ? '': trim(htmlspecialchars($_POST['ip'],ENT_QUOTES,'UTF-8'));
        if(empty($info['ip'])) errorAlert('IP不能为空');
        
        if(!askAnswerMdl::getInstance()->addAskAnswer($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增问题回答',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=askAnswer&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'askAnswer/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askAnswerMdl::getInstance()->getAskAnswer($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['ask_id'] =empty($_POST['ask_id']) ? 0: (int)$_POST['ask_id'];
        if(empty($info['ask_id'])) errorAlert('问题ID不能为空');
        $info['content'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
        if(empty($info['content'])) errorAlert('回答内容不能为空');
        $info['create_time'] =empty($_POST['create_time']) ? 0: (int)$_POST['create_time'];
        if(empty($info['create_time'])) errorAlert('发布时间不能为空');
        $info['ip'] = empty($_POST['ip']) ? '': trim(htmlspecialchars($_POST['ip'],ENT_QUOTES,'UTF-8'));
        if(empty($info['ip'])) errorAlert('IP不能为空');
        
        if(false === askAnswerMdl::getInstance()->updateAskAnswer($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改问题回答',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=askAnswer&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'askAnswer/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askAnswerMdl::getInstance()->getAskAnswer($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=askAnswer' : $_GET['back_url'];
    if(false !== askAnswerMdl::getInstance()->delAskAnswer($id)) {
       logsInt::getInstance()->systemLogs('删除了问题回答',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

