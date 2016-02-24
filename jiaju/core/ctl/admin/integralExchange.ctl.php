<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('integralExchange');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=integralExchange&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = integralExchangeMdl::getInstance()->getIntegralExchangeCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');  
    $col = array('a.`id`','a.`uid`','b.username','c.product_name','a.`product_id`','a.`type`','a.`integral`','a.`name`','a.`tel`','a.`t`','a.`status`');     
    $datas = integralExchangeMdl::getInstance()->getIntegralExchangeList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了用户积分兑换抽奖列表');
    require TEMPLATE_PATH.'integralExchange/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['product_id'] =empty($_POST['product_id']) ? 0: (int)$_POST['product_id'];
        if(empty($info['product_id'])) errorAlert('产品ID不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        $info['integral'] =empty($_POST['integral']) ? 0: (int)$_POST['integral'];
        if(empty($info['integral'])) errorAlert('消耗积分不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('联系人不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        $info['t'] = NOWTIME;
        $info['status'] =empty($_POST['status']) ? 0: (int)$_POST['status'];
        $info['note'] = empty($_POST['note']) ? '': trim(htmlspecialchars($_POST['note'],ENT_QUOTES,'UTF-8'));
        
        if(!integralExchangeMdl::getInstance()->addIntegralExchange($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了用户积分兑换抽奖',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=integralExchange&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'integralExchange/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = integralExchangeMdl::getInstance()->getIntegralExchange($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
        $info['product_id'] =empty($_POST['product_id']) ? 0: (int)$_POST['product_id'];
        if(empty($info['product_id'])) errorAlert('产品ID不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        $info['integral'] =empty($_POST['integral']) ? 0: (int)$_POST['integral'];
        if(empty($info['integral'])) errorAlert('消耗积分不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('联系人不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        $info['status'] =empty($_POST['status']) ? 0: (int)$_POST['status'];
        $info['note'] = empty($_POST['note']) ? '': trim(htmlspecialchars($_POST['note'],ENT_QUOTES,'UTF-8'));
        
        if(false === integralExchangeMdl::getInstance()->updateIntegralExchange($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了用户积分兑换抽奖',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=integralExchange&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'integralExchange/edit.html';
    die;
        
}
