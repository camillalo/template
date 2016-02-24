<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('ranks');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=ranks&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = ranksMdl::getInstance()->getRanksCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('rank_id'=>'asc');    
    $col = array('`rank_id`','`rank_name`','`icon`','`icon1`','`gold`','`day`','`num`','`prices`');     
    $datas = ranksMdl::getInstance()->getRanksList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('打开了用户等级列表');
    require TEMPLATE_PATH.'ranks/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['rank_name'] = empty($_POST['rank_name']) ? '': trim(htmlspecialchars($_POST['rank_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['rank_name'])) errorAlert('等级名称不能为空');
        $info['icon'] = empty($_POST['icon']) ? 0: $_POST['icon'];
        $info['icon1'] = empty($_POST['icon1']) ? 0: $_POST['icon1'];
        $info['day'] =empty($_POST['day']) ? 0: (int)$_POST['day'];
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        $info['gold'] =empty($_POST['gold']) ? 0: (int)$_POST['gold'];
        $info['prices'] =empty($_POST['prices']) ? 0: (int)($_POST['prices']*100);
        if(!ranksMdl::getInstance()->addRanks($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了用户等级',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=ranks&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'ranks/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $rank_id = empty ($_GET['rank_id']) ? errorAlert('参数错误') : (int)$_GET['rank_id'];    
    $data = ranksMdl::getInstance()->getRanks($rank_id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['rank_name'] = empty($_POST['rank_name']) ? '': trim(htmlspecialchars($_POST['rank_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['rank_name'])) errorAlert('等级名称不能为空');
        $info['icon'] = empty($_POST['icon']) ? 0: $_POST['icon'];
        $info['icon1'] = empty($_POST['icon1']) ? 0: $_POST['icon1'];
        $info['day'] =empty($_POST['day']) ? 0: (int)$_POST['day'];
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        $info['gold'] =empty($_POST['gold']) ? 0: (int)$_POST['gold'];
        $info['prices'] =empty($_POST['prices']) ? 0: (int)($_POST['prices']*100);
        if(false === ranksMdl::getInstance()->updateRanks($rank_id,$info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('修改了用户等级',$data,$info);
        echoJs("alert('添加成功');parent.location='index.php?ctl=ranks&act=edit&rank_id=".$rank_id."'");
        die;
    } 
    logsInt::getInstance()->systemLogs('打开了用户等级列表');
    require TEMPLATE_PATH.'ranks/edit.html';
    die;
        
}

if($_GET['act'] === 'view'){    
    $rank_id = empty ($_GET['rank_id']) ? errorAlert('参数错误') : (int)$_GET['rank_id'];    
    $data = ranksMdl::getInstance()->getRanks($rank_id);    
    if(empty($data)) errorAlert ('参数出错');
    logsInt::getInstance()->systemLogs('查看了用户等级详情');
    require TEMPLATE_PATH.'ranks/view.html';
    die;
}

if($_GET['act'] === 'del'){
    $rank_id = empty ($_GET['rank_id']) ? errorAlert('参数错误') : (int)$_GET['rank_id'];    
    $data = ranksMdl::getInstance()->getRanks($rank_id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=ranks' : $_GET['back_url'];
    if(false !== ranksMdl::getInstance()->delRanks($rank_id)) {
        logsInt::getInstance()->systemLogs('删除了用户等级',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

