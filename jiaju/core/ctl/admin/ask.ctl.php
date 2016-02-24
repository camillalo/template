<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('ask');
import::getInt('category');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=ask&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = askMdl::getInstance()->getAskCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC'); 
    $col = array('`id`','`ip`','`orderby`','`cate_id`','`uid`','`title`','`description`','`last_time`','`status`');     
    $datas = askMdl::getInstance()->getAskList($col,$where,$orderby,$begin,PAGE_SIZE);
    import::getMdl('users');
    foreach($datas as $k=>$v){
        $datas[$k]['username'] = usersMdl::getInstance()->getUsername($v['uid']);
        $datas[$k]['cate_name']= category::getInstance()->getCategoryName($__CATEGORY_TYPE['ask'],$v['cate_id']);
    }
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了问答列表');
    require TEMPLATE_PATH.'ask/main.html';
    die;
}


if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['description'] = empty($_POST['description']) ? '': getValue($_POST['description']);
        if(empty($info['description'])) errorAlert('描述不能为空');
        if(false === askMdl::getInstance()->updateAsk($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了问答内容',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=ask&act=edit&id=".$id."'");
        die;
    } 
    require TEMPLATE_PATH.'ask/edit.html';
    die;
        
}
if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : $_GET['id'];    
    $ids = array();
    if(is_array($id)){
        foreach($id as $v){
            $ids[] = (int)$v;
        }
    }else{
        $ids [] = (int)$id;
    }
    foreach($ids as $id){   
        $data = askMdl::getInstance()->getAsk($id);    
        if(empty($data)) errorAlert ('参数出错');      
        if(false !== askMdl::getInstance()->delAsk($id)) {
            logsInt::getInstance()->systemLogs('删除了问答问题',$data,array());
        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=ask' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'orderUp'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=ask' : $_GET['back_url'];
   
    if(false !== askMdl::getInstance()->updateOrderUp($id)) {
        logsInt::getInstance()->systemLogs('置顶问答问题',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'orderDown'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=ask' : $_GET['back_url'];
   
    if(false !== askMdl::getInstance()->updateAsk($id,array('orderby'=>0))) {
        logsInt::getInstance()->systemLogs('取消置顶问答问题',$data,array('orderby'=>0));
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}