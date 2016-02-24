<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('companyDianping');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=dianping&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = companyDianpingMdl::getInstance()->getCompanyDianpingCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');  
    $col = array('a.`id`','a.`uid`','c.username','a.`company_id`','a.`is_show`' ,'b.company_name','a.`process`','a.`service`','a.`design`','a.`sales`','a.`contact`','a.`realname`');     
    $datas = companyDianpingMdl::getInstance()->getCompanyDianpingList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了点评列表');
    require TEMPLATE_PATH.'dianping/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = companyDianpingMdl::getInstance()->getCompanyDianping($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['process'] =empty($_POST['process']) ? 0: (int)$_POST['process'];
        if(empty($info['process'])) errorAlert('工艺不能为空');
        $info['service'] =empty($_POST['service']) ? 0: (int)$_POST['service'];
        if(empty($info['service'])) errorAlert('服务不能为空');
        $info['design'] =empty($_POST['design']) ? 0: (int)$_POST['design'];
        if(empty($info['design'])) errorAlert('设计不能为空');
        $info['sales'] =empty($_POST['sales']) ? 0: (int)$_POST['sales'];
        if(empty($info['sales'])) errorAlert('售后不能为空');
      
        
        
        
        $info['dianping'] = empty($_POST['dianping']) ? '': trim(htmlspecialchars($_POST['dianping'],ENT_QUOTES,'UTF-8'));
        if(empty($info['dianping'])) errorAlert('评价不能为空');
        $info['project'] = empty($_POST['project']) ? '': trim(htmlspecialchars($_POST['project'],ENT_QUOTES,'UTF-8'));
        if(empty($info['project'])) errorAlert('装修项目不能为空');
        $info['contact'] = empty($_POST['contact']) ? '': trim(htmlspecialchars($_POST['contact'],ENT_QUOTES,'UTF-8'));
        if(empty($info['contact'])) errorAlert('联系方式不能为空');
        $info['realname'] = empty($_POST['realname']) ? '': trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
        if(empty($info['realname'])) errorAlert('称呼不能为空');
         $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(false === companyDianpingMdl::getInstance()->updateCompanyDianping($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了点评的内容',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=dianping&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'dianping/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = companyDianpingMdl::getInstance()->getCompanyDianping($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=dianping' : $_GET['back_url'];
    if(false !== companyDianpingMdl::getInstance()->delCompanyDianping($id)) {
       logsInt::getInstance()->systemLogs('删除了点评',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

