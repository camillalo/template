<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('group');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=group&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = groupMdl::getInstance()->getGroupCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('group_id'=>'DESC');
    $col = array('`group_id`','`group_name`');
    $datas = groupMdl::getInstance()->getGroupList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
     logsInt::getInstance()->systemLogs('查看了管理员角色列表');
    require TEMPLATE_PATH.'group/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = groupMdl::getInstance()->getGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_name'] = empty($_POST['group_name']) ? errorAlert('参数出错') : trim(htmlspecialchars($_POST['group_name'],ENT_QUOTES,'UTF-8'));    
        if(false === groupMdl::getInstance()->updateGroup($group_id,$info)) errorAlert ('更新失败');
        logsInt::getInstance()->systemLogs('编辑了管理员角色',$data,$info);
        errorAlert('操作成功');
        die;
    }
     logsInt::getInstance()->systemLogs('打开了管理员角色编辑模块');
    require TEMPLATE_PATH.'group/edit.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['group_name'] = empty($_POST['group_name']) ? errorAlert('群组名称不能为空') : trim(htmlspecialchars($_POST['group_name'],ENT_QUOTES,'UTF-8'));
        if(!groupMdl::getInstance()->addGroup($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了管理员角色',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=group&act=main'");
        die;
    } 
    require TEMPLATE_PATH.'group/add.html';
    die;
}


if($_GET['act'] === 'del'){
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = groupMdl::getInstance()->getGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    import::getMdl('admin');
    if(adminMdl::getInstance()->getAdminCountByGroupId($group_id)) errorAlert ('该角色下有管理员了');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=group' : $_GET['back_url'];
    if(false !== groupMdl::getInstance()->delGroup($group_id)) {
        logsInt::getInstance()->systemLogs('删除了管理员角色',$data,array());
        import::getMdl('groupMap');
        groupMapMdl::getInstance()->delGroupMapsByGroupId($group_id);
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}


if($_GET['act'] === 'privilege'){
    import::getMdl('groupMap');
    $group_id = empty ($_GET['group_id']) ? errorAlert('参数错误') : (int)$_GET['group_id'];    
    $data = groupMdl::getInstance()->getGroup($group_id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $privilege_ids = empty($_POST['privilege_ids']) ? array() : $_POST['privilege_ids'];
        groupMapMdl::getInstance()->delGroupMapsByGroupId($group_id);
        foreach($privilege_ids as $v){
            $info = array(
                'privilege_id' => (int)$v,
                'group_id'     => $group_id
            );
            groupMapMdl::getInstance()->addGroupMap($info);
        }
        logsInt::getInstance()->systemLogs('修改了管理角色授权',$privilege_ids,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=group&act=add'");
        die;
    }
    import::getMdl('privilegeGroup');
    $groups = privilegeGroupMdl::getInstance()->getAllPrivilegeGroup();
    import::getMdl('privilege');
    $privileges = privilegeMdl::getInstance()->getAllPrivilege();
    $has = groupMapMdl::getInstance()->getGroupMapsColByGroupId($group_id);
    logsInt::getInstance()->systemLogs('打开了管理员角色授权模块');
    require TEMPLATE_PATH.'group/privilege.html';
    die;
}