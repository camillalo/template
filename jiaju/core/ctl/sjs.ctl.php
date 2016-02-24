<?php

if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getInt('recommend');
recommend::getInstance()->init(2);

if($_GET['ctl'] === 'sjs'){
    $__SETTING['title'] = '设计师';
    import::getMdl('case');
    $cases = caseMdl::getInstance()->getNewCase(5);
    
    define('PAGE_SIZE',6);
    import::getMdl('designer');
    $url = array(); 
    $where = array();
    
    $areaid = empty($_GET['areaid']) ? 0 : (int)$_GET['areaid'];
    if(!empty($areaid)){
        $url['areaid'] = $areaid;
        $where['area_id'] = $areaid;
    }
    
    $w = empty($_GET['w']) ? 0 : (int)$_GET['w'];
    if(!empty($w)){
        $url['w'] = $w;
        $where['from_time'] = $w;
    }
    
    $totalnum = designerMdl::getInstance()->getDesignerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=>'desc','id'=>'DESC');  
    $col = array('`id`','`uid`','`face_pic`','`name`','`orderby`','is_gold','`position`','`about`','`from_time`','`style`');     
    $datas = designerMdl::getInstance()->getDesignerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('sjs','main',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);

    import::getMdl('users');
    import::getMdl('case');
    foreach($datas as $k=>$val){
        if(empty($val['face_pic'])) $datas[$k]['face_pic'] =  'statics/images/130_130.jpg';
        $datas[$k]['is_authentication'] = usersMdl::getInstance()->checkIsAuthentication($val['uid']);
        $datas[$k]['case'] = caseMdl::getInstance()->getNewCaseByDesignerId($val['id'],3); 
    }
     $quxian =areaInt::getInstance()->getAreas(); 
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('sjs');
    require TEMPLATE_PATH.'sjs.html';
    die;
}