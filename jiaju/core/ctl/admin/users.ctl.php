<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('users');
import::getMdl('ranks');
$ranks = ranksMdl::getInstance()->getAllRanksPairs();
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=users&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $_GET['type'] = empty($_GET['type']) ?  0 : (int)$_GET['type'];
    if(!empty($_GET['type'])){
        $url.='&type='.  $_GET['type'];
        $where['type'] = $_GET['type'];
    }
    $_GET['rank_id'] = isset($_GET['rank_id'])&&  is_numeric($_GET['rank_id']) ? (int)$_GET['rank_id'] : 999;
    if($_GET['rank_id']!==999){
        $url.='&rank_id='.  $_GET['rank_id'];
        $where['rank_id'] = $_GET['rank_id'];
    }
    $_GET['uid'] = empty($_GET['uid']) ?  0 : (int)$_GET['uid'];
    if(!empty($_GET['uid'])){
        $url.='&uid='.  $_GET['uid'];
        $where['uid'] = $_GET['uid'];
    }
    
    $totalnum = usersMdl::getInstance()->getUsersCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('uid'=>'DESC');  
    $col = array('`uid`','`username`','`realname`','`mobile`','`type`','`rank_id`','`day`','`num`','`gold`');     
    $datas = usersMdl::getInstance()->getUsersList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
     foreach($datas as $k=>$val){
          $datas[$k]['is_authentication'] = usersMdl::getInstance()->checkIsAuthentication($val['uid']);
     }
     logsInt::getInstance()->systemLogs('查看了用户列表');
    require TEMPLATE_PATH.'users/main.html';
    die;
}

if($_GET['act'] === 'rank'){
    $id= empty($_GET['id']) ? dieJsonErr('参数错误') : (int)$_GET['id'];
    $rank = empty($_GET['rank']) ? dieJsonErr('请选择VIP级别') : (int)$_GET['rank'];
    $data = usersMdl::getInstance()->getUsers($id);    
    if(empty($data)) dieJsonErr ('参数出错');
    $rank_info = ranksMdl::getInstance()->getRanks($rank);
    if(empty($rank_info)) dieJsonErr ('请选择VIP级别#02');
    $info = array();
    $info['rank_id'] = $rank;
    $info['day'] = $data['day'] > NOWTIME ? $data['day'] + 86400 * $rank_info['day'] : NOWTIME + 86400* $rank_info['day'];
    $info['num'] = $data['num'] + $rank_info['num'];
    $info['gold'] = $data['gold'] + $rank_info['gold'];
    if(false === usersMdl::getInstance()->updateUsers($id,$info))        dieJsonErr('更新失败');
    $log = array(
        'admin_id'=> $_SESSION['admin']['admin_id'],
        'uid' => $id,
        'rank_id' => $rank,
        'create_time' => NOWTIME
    );
    import::getMdl('rankLogs');
    rankLogsMdl::getInstance()->addRankLogs($log);
    logsInt::getInstance()->systemLogs('开通了用户的等级权限',$info,array());
    dieJsonRight('操作成功');
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['username'] = empty($_POST['username']) ? '': trim(htmlspecialchars($_POST['username'],ENT_QUOTES,'UTF-8'));
        if(empty($info['username'])) errorAlert('用户名不能为空');
        $info['password'] = empty($_POST['password']) ? errorAlert('密码不能为空'): md5($_POST['password']);
        $info['realname'] = empty($_POST['realname']) ? '': trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
        if(empty($info['realname'])) errorAlert('真是姓名不能为空');
        $info['mobile'] = empty($_POST['mobile']) ? '': trim(htmlspecialchars($_POST['mobile'],ENT_QUOTES,'UTF-8'));
        if(empty($info['mobile'])) errorAlert('手机号码不能为空');
        
         $info['email'] = empty($_POST['email']) ? '': trim(htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8'));
        if(empty($info['email'])) errorAlert('email不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('会员类型不能为空');

        if(!usersMdl::getInstance()->addUsers($info)) errorAlert ('保存失败');
        logsInt::getInstance()->systemLogs('代会员注册',$info,array());
        echoJs("alert('保存成功');parent.location='index.php?ctl=users&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'users/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
    $data = usersMdl::getInstance()->getUsers($uid);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['username'] = empty($_POST['username']) ? '': trim(htmlspecialchars($_POST['username'],ENT_QUOTES,'UTF-8'));
        if(empty($info['username'])) errorAlert('用户名不能为空');
        if(!empty($_POST['password'])){
            $info['password'] = md5($_POST['password']);
        }
        $info['realname'] = empty($_POST['realname']) ? '': trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
        if(empty($info['realname'])) errorAlert('真是姓名不能为空');
        $info['mobile'] = empty($_POST['mobile']) ? '': trim(htmlspecialchars($_POST['mobile'],ENT_QUOTES,'UTF-8'));
        if(empty($info['mobile'])) errorAlert('手机号码不能为空');
         $info['email'] = empty($_POST['email']) ? '': trim(htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8'));
        if(empty($info['email'])) errorAlert('email不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('会员类型不能为空');
        
        $info['day'] =empty($_POST['day']) ? 0: strtotime($_POST['day']);
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        $info['gold'] =empty($_POST['gold']) ? 0: (int)$_POST['gold'];
        
        if(false === usersMdl::getInstance()->updateUsers($uid,$info)) errorAlert ('保存失败');
        logsInt::getInstance()->systemLogs('编辑了会员资料',$data,$info);
        echoJs("alert('保存成功');parent.location='index.php?ctl=users&act=edit&uid=".$uid."'");
        die;
    } 
    logsInt::getInstance()->systemLogs('打开了会员资料编辑面板');
    require TEMPLATE_PATH.'users/edit.html';
    die;
        
}

if($_GET['act'] === 'view'){    
    $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
    $data = usersMdl::getInstance()->getUsers($uid);    
    $userEx = usersMdl::getInstance()->getUsersEx($uid);
    if(empty($data)) errorAlert ('参数出错');
    logsInt::getInstance()->systemLogs('查看了会员资料',$data,array());
    require TEMPLATE_PATH.'users/view.html';
    die;
}

if($_GET['act'] === 'del'){
    $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
    $data = usersMdl::getInstance()->getUsers($uid);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=users' : $_GET['back_url'];
    if(false !== usersMdl::getInstance()->delUsers($uid)) {
       logsInt::getInstance()->systemLogs('删除了会员',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}



if($_GET['act'] === 'authentication'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=users' : $_GET['back_url'];
    import::getMdl('users');
    $is_authentication = usersMdl::getInstance()->checkIsAuthentication($id);
    $info['is_authentication']  = $is_authentication ? 0 : 1;
    $info['uid'] = $id;
    if(false !== usersMdl::getInstance()->replaceUsersEx($info)){
        logsInt::getInstance()->systemLogs('修改了会员认证情况',$info,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    
    die;
}
