<?php
if ( !defined ( 'NOWTIME') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('privilege');
if($_GET['act'] === 'main'){;
    $url = 'index.php?ctl=privilege&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = privilegeMdl::getInstance()->getPrivilegeCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.privilege_id'=>'DESC');
    $col = array('a.privilege_id','b.group_name','a.privilege_name','a.privilege_key');
    $datas = privilegeMdl::getInstance()->getPrivilegeList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
     logsInt::getInstance()->systemLogs('查看了权限列表');
    require TEMPLATE_PATH.'privilege/main.html';
    die;
}
if($_GET['act'] === 'edit'){
    $privilege_id = empty($_GET['privilege_id']) ? $_SESSION['privilege']['privilege_id'] : (int)$_GET['privilege_id']; 
    $data = privilegeMdl::getInstance()->getPrivilege($privilege_id);
    if(empty($data)) errorAlert ('没有该权限');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $privilegeinfo['group_id'] = empty($_POST['group_id']) ? errorAlert('请选择用户组') : (int)$_POST['group_id'];
        $privilegeinfo['privilege_name'] = empty($_POST['privilege_name']) ? errorAlert('请填写权限名称') : htmlspecialchars($_POST['privilege_name'],ENT_QUOTES,'utf-8');
        $privilegeinfo['privilege_key'] = empty($_POST['privilege_key']) ? errorAlert('请填写权限KEY') : htmlspecialchars($_POST['privilege_key'],ENT_QUOTES,'utf-8');
        if(false === privilegeMdl::getInstance()->updatePrivilege($privilege_id,$privilegeinfo)) errorAlert ('修改失败');
         logsInt::getInstance()->systemLogs('修改了权限',$data,$privilegeinfo);
        errorAlert('操作成功');
        die;
    }    
    import::getMdl('privilegeGroup');
    $groups = privilegeGroupMdl::getInstance()->getAllPrivilegeGroup();
     logsInt::getInstance()->systemLogs('打开了权限编辑');
    require TEMPLATE_PATH.'privilege/edit.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $privilegeinfo['group_id'] = empty($_POST['group_id']) ? errorAlert('请选择用户组') : (int)$_POST['group_id'];
        $privilegeinfo['privilege_name'] = empty($_POST['privilege_name']) ? errorAlert('请填写权限名称') : htmlspecialchars($_POST['privilege_name'],ENT_QUOTES,'utf-8');
        $privilegeinfo['privilege_key'] = empty($_POST['privilege_key']) ? errorAlert('请填写权限KEY') : htmlspecialchars($_POST['privilege_key'],ENT_QUOTES,'utf-8');
        if(!privilegeMdl::getInstance()->addPrivilege($privilegeinfo)) errorAlert ('增加失败');
         logsInt::getInstance()->systemLogs('新增了权限',$privilegeinfo,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=privilege&act=main'");
        die;
    } 
    import::getMdl('privilegeGroup');
    $groups = privilegeGroupMdl::getInstance()->getAllPrivilegeGroup();
    require TEMPLATE_PATH.'privilege/add.html';
    die;
}

if($_GET['act'] === 'del'){
    $privilege_id = empty ($_GET['privilege_id']) ? errorAlert('参数错误') : (int)$_GET['privilege_id'];    
    $data = privilegeMdl::getInstance()->getPrivilege($privilege_id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=privilege' : $_GET['back_url'];
    if(false !== privilegeMdl::getInstance()->delPrivilege($privilege_id)) {
        logsInt::getInstance()->systemLogs('删除了权限',$data,array());
        import::getMdl('groupMap');
        groupMapMdl::getInstance()->delGroupMapsByPrivilegeId($privilege_id);
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}