<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('buildingSite');
import::getInt('category');
import::getMdl('company');
import::getInt('area');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=buildingSite&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = buildingSiteMdl::getInstance()->getBuildingSiteCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=>'DESC','id'=>'DESC');   
    $col = array('`id`','`company_id`','is_show','orderby','status','score','`area_id`','`name`','`space_id`','`price_id`','`a_id`','`style_id`','`bg_time`','`description`');     
    $datas = buildingSiteMdl::getInstance()->getBuildingSiteList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    foreach($datas as $k=>$v){
        $datas[$k]['space_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['space_id']);
        $datas[$k]['style_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['style_id']);
        $datas[$k]['a_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['a_id']);
        $datas[$k]['price_name'] =category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['price_id']);
        $datas[$k]['company_name'] = companyMdl::getInstance()->getCompanyName($v['company_id']);
        $datas[$k]['area_name'] = areaInt::getInstance()->getAreaName($v['area_id']);
    }
    logsInt::getInstance()->systemLogs('查看了在建工地列表');
    require TEMPLATE_PATH.'buildingSite/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['company_id'] =empty($_POST['company_id']) ? 0: (int)$_POST['company_id'];
        if(empty($info['company_id'])) errorAlert('公司ID不能为空');
    
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('区县不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('工地名称不能为空');
        $info['space_id'] =empty($_POST['space_id']) ? 0: (int)$_POST['space_id'];
        if(empty($info['space_id'])) errorAlert('空间不能为空');
        $info['price_id'] =empty($_POST['price_id']) ? 0: (int)$_POST['price_id'];
        if(empty($info['price_id'])) errorAlert('预算不能为空');
        $info['a_id'] =empty($_POST['a_id']) ? 0: (int)$_POST['a_id'];
        if(empty($info['a_id'])) errorAlert('面积不能为空');
        $info['style_id'] =empty($_POST['style_id']) ? 0: (int)$_POST['style_id'];
        if(empty($info['style_id'])) errorAlert('风格不能为空');
        $info['bg_time'] = empty($_POST['bg_time']) ? '': trim(htmlspecialchars($_POST['bg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['bg_time'])) errorAlert('开始时间不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        
        if(empty($info['description'])) errorAlert('描述不能为空');
         $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
         $info['orderby'] =empty($_POST['orderby']) ? 0: (int)$_POST['orderby'];
        if(!buildingSiteMdl::getInstance()->addBuildingSite($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了在建工地',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=buildingSite&act=add'");
        die;
    } 
    $spaces = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['space']);
    $styles = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['style']);
    $areas = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['area']);
    $prices = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['price']);
    $quxian =areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'buildingSite/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = buildingSiteMdl::getInstance()->getBuildingSite($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['company_id'] =empty($_POST['company_id']) ? 0: (int)$_POST['company_id'];
        if(empty($info['company_id'])) errorAlert('公司ID不能为空');
       
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('区县不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('工地名称不能为空');
        $info['space_id'] =empty($_POST['space_id']) ? 0: (int)$_POST['space_id'];
        if(empty($info['space_id'])) errorAlert('空间不能为空');
        $info['price_id'] =empty($_POST['price_id']) ? 0: (int)$_POST['price_id'];
        if(empty($info['price_id'])) errorAlert('预算不能为空');
        $info['a_id'] =empty($_POST['a_id']) ? 0: (int)$_POST['a_id'];
        if(empty($info['a_id'])) errorAlert('面积不能为空');
        $info['style_id'] =empty($_POST['style_id']) ? 0: (int)$_POST['style_id'];
        if(empty($info['style_id'])) errorAlert('风格不能为空');
        $info['bg_time'] = empty($_POST['bg_time']) ? '': trim(htmlspecialchars($_POST['bg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['bg_time'])) errorAlert('开始时间不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(empty($info['description'])) errorAlert('描述不能为空');
         $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
         $info['orderby'] =empty($_POST['orderby']) ? 0: (int)$_POST['orderby'];
        if(false === buildingSiteMdl::getInstance()->updateBuildingSite($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('修改了在建工地',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=buildingSite&act=edit&id=".$id."'");
        die;
    } 
    $spaces = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['space']);
    $styles = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['style']);
    $areas = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['area']);
    $prices = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['price']);
    $quxian =areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'buildingSite/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = buildingSiteMdl::getInstance()->getBuildingSite($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=buildingSite' : $_GET['back_url'];
    if(false !== buildingSiteMdl::getInstance()->delBuildingSite($id)) {
       logsInt::getInstance()->systemLogs('删除了在建工地',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

