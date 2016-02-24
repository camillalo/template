<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('diary');
import::getInt('category');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=diary&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = diaryMdl::getInstance()->getDiaryCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC'); 
    $col = array('`id`','`title`','`cate_id`','is_show');     
    $datas = diaryMdl::getInstance()->getDiaryList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了装修日记');
    require TEMPLATE_PATH.'diary/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['cate_id'] =empty($_POST['cate_id']) ? 0: (int)$_POST['cate_id'];
        if(empty($info['cate_id'])) errorAlert('类别不能为空');
        $info['uid'] = 0;
        $info['contents'] = empty($_POST['contents']) ? '': getValue($_POST['contents']);
        
        if(empty($info['contents'])) errorAlert('日记内容不能为空');
        $info['create_time'] = NOWTIME;
         $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(!diaryMdl::getInstance()->addDiary($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了装修日记',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=diary&act=add'");
        die;
    } 
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['lc']);
    require TEMPLATE_PATH.'diary/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = diaryMdl::getInstance()->getDiary($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['cate_id'] =empty($_POST['cate_id']) ? 0: (int)$_POST['cate_id'];
        if(empty($info['cate_id'])) errorAlert('类别不能为空');
        $info['contents'] = empty($_POST['contents']) ? '': getValue($_POST['contents']);
        if(empty($info['contents'])) errorAlert('日记内容不能为空');
         $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(false === diaryMdl::getInstance()->updateDiary($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了装修日记',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=diary&act=edit&id=".$id."'");
        die;
    } 
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['lc']);
    logsInt::getInstance()->systemLogs('打开了装修日记编辑面板');
    require TEMPLATE_PATH.'diary/edit.html';
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
        $data = diaryMdl::getInstance()->getDiary($id);    
        if(empty($data)) errorAlert ('参数出错');
        if(false !== diaryMdl::getInstance()->delDiary($id)) {
             logsInt::getInstance()->systemLogs('删除了装修日记',$data,array());
        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=diary' : $_GET['back_url'];
     dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

