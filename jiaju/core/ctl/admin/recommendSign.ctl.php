<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('recommendSign');
import::getMdl('recommendGroup');
if($_GET['act'] === 'main'){;
    $url = 'index.php?ctl=recommendSign&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $_GET['group_id'] = empty($_GET['group_id']) ? 0 : (int)$_GET['group_id'];
    if(!empty($_GET['group_id'])){
        $url.='&group_id='.  $_GET['group_id'];
        $where['group_id'] = $_GET['group_id'];
    }
    
    $totalnum = recommendSignMdl::getInstance()->getRecommendSignCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'asc');
    $col = array('a.id','b.group_name','a.name','a.key');
    $datas = recommendSignMdl::getInstance()->getRecommendSignList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    
    $groups = recommendGroupMdl::getInstance()->getAllRecommendGroup();
    logsInt::getInstance()->systemLogs('查看了推荐位');
    require TEMPLATE_PATH.'recommendSign/main.html';
    die;
}
if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id']; 
    $data = recommendSignMdl::getInstance()->getRecommendSign($id);
    if(empty($data)) errorAlert ('没有该推荐位位');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_id'] = empty($_POST['group_id']) ? errorAlert('请选择推荐位组') : (int)$_POST['group_id'];
        $info['name'] = empty($_POST['name']) ? errorAlert('请填写推荐位名称') : htmlspecialchars($_POST['name'],ENT_QUOTES,'utf-8');
        $info['key'] = empty($_POST['key']) ? errorAlert('请填写推荐位KEY') : htmlspecialchars($_POST['key'],ENT_QUOTES,'utf-8');
        
        $info['type'] = empty($_POST['type']) ? 0: (int)$_POST['type'];
        $info['mold'] = empty($_POST['mold']) ? 0: (int)$_POST['mold'];
        $info['cate_id'] = empty($_POST['cate_id']) ? 0: (int)$_POST['cate_id'];
        
        if(false === recommendSignMdl::getInstance()->updateRecommendSign($id,$info)) errorAlert ('修改失败');
        logsInt::getInstance()->systemLogs('修改了推荐位',$data,$info);
        errorAlert('操作成功');
        die;
    }    
    import::getMdl('recommendGroup');
    $groups = recommendGroupMdl::getInstance()->getAllRecommendGroup();
    logsInt::getInstance()->systemLogs('打开了推荐位编辑');
    require TEMPLATE_PATH.'recommendSign/edit.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_id'] = empty($_POST['group_id']) ? errorAlert('请选择用户组') : (int)$_POST['group_id'];
        $info['name'] = empty($_POST['name']) ? errorAlert('请填写推荐位名称') : htmlspecialchars($_POST['name'],ENT_QUOTES,'utf-8');
        $info['key'] = empty($_POST['key']) ? errorAlert('请填写推荐位KEY') : htmlspecialchars($_POST['key'],ENT_QUOTES,'utf-8');
        $info['type'] = empty($_POST['type']) ? 0: (int)$_POST['type'];
        $info['mold'] = empty($_POST['mold']) ? 0: (int)$_POST['mold'];
        $info['cate_id'] = empty($_POST['cate_id']) ? 0: (int)$_POST['cate_id'];
        if(!recommendSignMdl::getInstance()->addRecommendSign($info)) errorAlert ('增加失败');
        logsInt::getInstance()->systemLogs('新增了推荐位',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=recommendSign&act=main'");
        die;
    } 
    import::getMdl('recommendGroup');
    $groups = recommendGroupMdl::getInstance()->getAllRecommendGroup();
    require TEMPLATE_PATH.'recommendSign/add.html';
    die;
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = recommendSignMdl::getInstance()->getRecommendSign($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=recommendSign' : $_GET['back_url'];
    if(false !== recommendSignMdl::getInstance()->delRecommendSign($id)) {
        logsInt::getInstance()->systemLogs('删除了推荐位',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}