<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('recommendGroup');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=recommendGroup&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = recommendGroupMdl::getInstance()->getRecommendGroupCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('group_id'=>'DESC');
    $col = array('`group_id`','`group_name`');
    $datas = recommendGroupMdl::getInstance()->getRecommendGroupList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了推荐组');
    require TEMPLATE_PATH.'recommendGroup/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = recommendGroupMdl::getInstance()->getrecommendGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_name'] = empty($_POST['group_name']) ? errorAlert('参数出错') : trim(htmlspecialchars($_POST['group_name'],ENT_QUOTES,'UTF-8'));    
        if(false === recommendGroupMdl::getInstance()->updateRecommendGroup($group_id,$info)) errorAlert ('更新失败');
        logsInt::getInstance()->systemLogs('修改了推荐组',$data,$info);
        errorAlert('操作成功');
        die;
    }
    logsInt::getInstance()->systemLogs('打开推荐组编辑');
    require TEMPLATE_PATH.'recommendGroup/edit.html';
    die;
}

if($_GET['act'] === 'add'){   
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_name'] = empty($_POST['group_name']) ? errorAlert('群组名称不能为空') : trim(htmlspecialchars($_POST['group_name'],ENT_QUOTES,'UTF-8'));
        if(!recommendGroupMdl::getInstance()->addRecommendGroup($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了推荐组',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=recommendGroup&act=main'");
        die;
    } 
    require TEMPLATE_PATH.'recommendGroup/add.html';
    die;
}


if($_GET['act'] === 'del'){
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = recommendGroupMdl::getInstance()->getRecommendGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    import::getMdl('recommendSign');
    if(recommendSignMdl::getInstance()->getRecommendSignByGroupId($group_id)) errorAlert ('该分类下面有其他推荐位');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=recommendGroup' : $_GET['back_url'];
    if(false !== recommendGroupMdl::getInstance()->delRecommendGroup($group_id)) {
        logsInt::getInstance()->systemLogs('删除了推荐组',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}