<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('admin');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=admin&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = adminMdl::getInstance()->getAdminCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.admin_id'=>'DESC');
    $col = array('a.admin_id','a.username','a.realname','a.email','a.mobile','a.tel','b.group_name','a.last_t','a.last_ip','a.is_lock');
    $datas = adminMdl::getInstance()->getAdminList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
     logsInt::getInstance()->systemLogs('查看了管理员列表');
    require TEMPLATE_PATH.'admin/main.html';
    die;
}

if($_GET['act'] === 'lock'){
    $admin_id = empty($_GET['admin_id']) ? errorAlert('参数错误') : (int)$_GET['admin_id']; 
    if($admin_id === (int)$_SESSION['admin']['admin_id']) errorAlert ('不可以操作自己');
    $adminInfo = adminMdl::getInstance()->getAdmin($admin_id);
    if(empty($adminInfo)) errorAlert ('没有该用户');
    if((int)$adminInfo['is_lock'] === 1) errorAlert ('不可操作'); 
    $info['is_lock'] = 1;
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=admin' : $_GET['back_url'];
    logsInt::getInstance()->systemLogs('锁定了管理员',$adminInfo,array());
    if(adminMdl::getInstance()->updateAdmin($admin_id,$info)) dieJs ('alert("操作成功");parent.location="'.$back_url.'"');
    errorAlert('操作失败');
    die;
}


if($_GET['act'] === 'unlock'){
    $admin_id = empty($_GET['admin_id']) ? errorAlert('参数错误') : (int)$_GET['admin_id'];
    if($admin_id === (int)$_SESSION['admin']['admin_id']) errorAlert ('不可以操作自己');
    $adminInfo = adminMdl::getInstance()->getAdmin($admin_id);
    if(empty($adminInfo)) errorAlert ('没有该用户');
    if((int)$adminInfo['is_lock'] === 0) errorAlert ('不可操作'); 
    $info['is_lock'] = 0;
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=admin' : $_GET['back_url'];
    logsInt::getInstance()->systemLogs('解锁了管理员',$adminInfo,array());
    if(adminMdl::getInstance()->updateAdmin($admin_id,$info)) dieJs ('alert("操作成功");parent.location="'.$back_url.'"');
    errorAlert('操作失败');
    die;
}


if($_GET['act'] === 'edit'){
    $admin_id = empty($_GET['admin_id']) ? errorAlert ('没有该管理员'): (int)$_GET['admin_id']; 

    $data = adminMdl::getInstance()->getAdmin($admin_id);
    
    if(empty($data)) errorAlert ('没有该管理员');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        if(!empty($_POST['password'])){
            $admininfo['password'] =  md5(trim($_POST['password']));
        
            $password2= empty($_POST['password2'])? errorAlert('请确认密码')     : md5(trim($_POST['password2']));

            if($admininfo['password'] !== $password2) errorAlert ('两次密码不一致');
        }
         
        $admininfo['group_id'] = empty($_POST['group_id']) ? errorAlert('请选择用户组') : (int)$_POST['group_id'];
        
        $admininfo['realname'] = empty($_POST['realname']) ? errorAlert('请填写真实名称') : trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
        
        $admininfo['email'] = empty($_POST['email']) ? errorAlert('请填写邮件') : trim($_POST['email']);
        
        if(!isEmail($admininfo['email'])) errorAlert ('邮件格式不正确');
        
        $admininfo['mobile'] = empty($_POST['mobile']) ? "" : trim($_POST['mobile']);
        
        $admininfo['tel']  = empty ($_POST['tel']) ? '' : trim($_POST['tel']);
        
        if(!isMobile($admininfo['mobile']) && !isPhone($admininfo['tel']) ) errorAlert ('手机和电话至少要有一项正确');
        
        
        if(false === adminMdl::getInstance()->updateAdmin($admin_id,$admininfo)) errorAlert ('修改失败');
        logsInt::getInstance()->systemLogs('编辑了管理员',$data,$admininfo);
        errorAlert('操作成功');
        die;
    }    
    import::getMdl('group');
    $groups = groupMdl::getInstance()->getAllGroup();
     logsInt::getInstance()->systemLogs('打开了管理员编辑页面');
    require TEMPLATE_PATH.'admin/edit.html';
    die;
}

//编辑个人资料 单独出来主要授权的时候有用 防止漏洞存在
if($_GET['act'] === 'edit2'){
    $admin_id =  $_SESSION['admin']['admin_id'] ; 

    $data = adminMdl::getInstance()->getAdmin($admin_id);
    
    if(empty($data)) errorAlert ('没有该管理员');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        if(!empty($_POST['password'])){
            $admininfo['password'] =  md5(trim($_POST['password']));
        
            $password2= empty($_POST['password2'])? errorAlert('请确认密码')     : md5(trim($_POST['password2']));

            if($admininfo['password'] !== $password2) errorAlert ('两次密码不一致');
        }
        $admininfo['realname'] = empty($_POST['realname']) ? errorAlert('请填写真实名称') : trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
        
        $admininfo['email'] = empty($_POST['email']) ? errorAlert('请填写邮件') : trim($_POST['email']);
        
        if(!isEmail($admininfo['email'])) errorAlert ('邮件格式不正确');
        
        $admininfo['mobile'] = empty($_POST['mobile']) ? "" : trim($_POST['mobile']);
        
        $admininfo['tel']  = empty ($_POST['tel']) ? '' : trim($_POST['tel']);
        
        if(!isMobile($admininfo['mobile']) && !isPhone($admininfo['tel']) ) errorAlert ('手机和电话至少要有一项正确');
        
        
        if(false === adminMdl::getInstance()->updateAdmin($admin_id,$admininfo)) errorAlert ('修改失败');
        logsInt::getInstance()->systemLogs('编辑了个人资料',$data,$admininfo);
        errorAlert('操作成功');
        die;
    }    
    import::getMdl('group');
    $groups = groupMdl::getInstance()->getAllGroup();
     logsInt::getInstance()->systemLogs('打开了个人资料编辑页面');
    require TEMPLATE_PATH.'admin/edit2.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $admininfo['username'] = empty($_POST['username']) ? errorAlert('用户名不能为空') : trim(htmlspecialchars($_POST['username'],ENT_QUOTES,'UTF-8'));
        
        if(adminMdl::getInstance()->getAdminByUsername($admininfo['username'])) errorAlert ('用户已存在');
        
        $admininfo['password'] = empty($_POST['password']) ? errorAlert('密码不能为空')   : md5(trim($_POST['password']));
        
        $password2= empty($_POST['password2'])? errorAlert('请确认密码')     : md5(trim($_POST['password2']));
        
        if($admininfo['password'] !== $password2) errorAlert ('两次密码不一致');
        
        $admininfo['group_id'] = empty($_POST['group_id']) ? errorAlert('请选择用户组') : (int)$_POST['group_id'];
        
        $admininfo['realname'] = empty($_POST['realname']) ? errorAlert('请填写真实名称') : trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
        
        $admininfo['email'] = empty($_POST['email']) ? errorAlert('请填写邮件') : trim($_POST['email']);
        
        if(!isEmail($admininfo['email'])) errorAlert ('邮件格式不正确');
        
        $admininfo['mobile'] = empty($_POST['mobile']) ? "" : trim($_POST['mobile']);
        
        $admininfo['tel']  = empty ($_POST['tel']) ? '' : trim($_POST['tel']);
        
        if(!isMobile($admininfo['mobile']) && !isPhone($admininfo['tel']) ) errorAlert ('手机和电话至少要有一项正确');
        
        if(!adminMdl::getInstance()->addAdmin($admininfo)) errorAlert ('增加管理员失败');
        logsInt::getInstance()->systemLogs('新增了管理员',$admininfo,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=admin&act=main'");
        
        die;
    } 
    import::getMdl('group');
    $groups = groupMdl::getInstance()->getAllGroup();
    require TEMPLATE_PATH.'admin/add.html';
    die;
}
