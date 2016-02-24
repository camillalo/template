<?php

if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getInt('recommend');
recommend::getInstance()->init(2);

if($_GET['act'] === 'main'){
    define('PAGE_SIZE',6);
    import::getMdl('buildingSite');
    $url = array(); 
    $where  = array('is_show'=>1);
    $sid = empty($_GET['sid']) ? 0 : (int)$_GET['sid'];
    if(!empty($sid)){
        $url['sid'] = $sid;
        $where['space_id'] = $sid;
    }
    $stid = empty($_GET['stid']) ? 0 : (int)$_GET['stid'];
    if(!empty($stid)){
        $url['stid'] = $stid;
        $where['stype_id'] = $stid;
    }
    $aid = empty($_GET['aid']) ? 0 : (int)$_GET['aid'];
    if(!empty($aid)){
        $url['aid'] = $aid;
        $where['a_id'] = $aid;
    }
    
    $pid = empty($_GET['pid']) ? 0 : (int)$_GET['pid'];
    if(!empty($pid)){
        $url['pid'] = $pid;
        $where['price_id'] = $pid;
    }
    $areaid = empty($_GET['areaid']) ? 0 : (int)$_GET['areaid'];
    if(!empty($areaid)){
        $url['areaid'] = $areaid;
        $where['area_id'] = $areaid;
    }
    $status = empty($_GET['status']) ? 0 : (int)$_GET['status'];
    if(!empty($status)){
        $url['status'] = $status;
        $where['status'] = $status;
    }
    
    $totalnum = buildingSiteMdl::getInstance()->getBuildingSiteCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=>'DESC','id'=>'DESC');   
    $col = array();     
    $datas = buildingSiteMdl::getInstance()->getBuildingSiteList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('site','main',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    import::getMdl('company');
    import::getMdl('designer');
    foreach($datas as $k=>$v){
        $datas[$k]['space_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['space_id']);
        $datas[$k]['a_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['a_id']);
        $datas[$k]['price_name'] =category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['price_id']);
        $datas[$k]['area_name'] = areaInt::getInstance()->getAreaName($v['area_id']);
        $datas[$k]['style_name'] =category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['style_id']);
        $datas[$k]['company_name'] = companyMdl::getInstance()->getCompanyName($v['company_id']);
        $datas[$k]['designer_name'] =  designerMdl::getInstance()->getDesignerName($v['designer_id']);
    }
   $style = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['style']); 
   $spaces = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['space']);
   $areas = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['area']);
   $prices = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['price']);
   $quxian =areaInt::getInstance()->getAreas(); 
   
   $hotsSite = buildingSiteMdl::getInstance()->getHotsCompanySite(10);
   import::getMdl('case');
   $hotsCases = caseMdl::getInstance()->getHotsCase(10);
   import::getInt('seo');
   $__SETTING = seoInt::getInstance()->load('site');
   require TEMPLATE_PATH.'service_site.html';
   die;
}
