<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('privilegeGroup');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=privilegeGroup&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = privilegeGroupMdl::getInstance()->getPrivilegeGroupCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('group_id'=>'DESC');
    $col = array('`group_id`','`group_name`');
    $datas = privilegeGroupMdl::getInstance()->getPrivilegeGroupList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
     logsInt::getInstance()->systemLogs('查看了权限组列表');
    require TEMPLATE_PATH.'privilegeGroup/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = privilegeGroupMdl::getInstance()->getprivilegeGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_name'] = empty($_POST['group_name']) ? errorAlert('参数出错') : trim(htmlspecialchars($_POST['group_name'],ENT_QUOTES,'UTF-8'));    
        if(false === privilegeGroupMdl::getInstance()->updatePrivilegeGroup($group_id,$info)) errorAlert ('更新失败');
        logsInt::getInstance()->systemLogs('编辑了权限分类',$data,$info);
        errorAlert('操作成功');
        die;
    }
    logsInt::getInstance()->systemLogs('打开了权限组编辑页面');
    require TEMPLATE_PATH.'privilegeGroup/edit.html';
    die;
}

if($_GET['act'] === 'add'){   
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_name'] = empty($_POST['group_name']) ? errorAlert('群组名称不能为空') : trim(htmlspecialchars($_POST['group_name'],ENT_QUOTES,'UTF-8'));
        if(!privilegeGroupMdl::getInstance()->addPrivilegeGroup($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了权限分类',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=privilegeGroup&act=main'");
        die;
    } 
    require TEMPLATE_PATH.'privilegeGroup/add.html';
    die;
}


if($_GET['act'] === 'del'){
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = privilegeGroupMdl::getInstance()->getPrivilegeGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    import::getMdl('privilege');
    if(privilegeMdl::getInstance()->getCountPrivilegeByGroupId($group_id)) errorAlert ('该分类下面有其他权限');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=privilegeGroup' : $_GET['back_url'];
    if(false !== privilegeGroupMdl::getInstance()->delPrivilegeGroup($group_id)) {
        logsInt::getInstance()->systemLogs('删除了权限分类',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}