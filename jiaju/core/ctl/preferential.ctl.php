<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getInt('recommend');
recommend::getInstance()->init(2);
if($_GET['act'] === 'main'){
    $__SETTING['title'] = '优惠信息';
    define('PAGE_SIZE',20);
    $areas = areaInt::getInstance()->getAreas();
    import::getMdl('preferential');
    
    $url = array(); 
    $where  = array('is_show'=>1);
    $_GET['area_id'] = empty($_GET['area_id']) ? 0 : (int)$_GET['area_id'];
    if($_GET['area_id']){
        $url['area_id'] = $_GET['area_id'];
        $where['area_id'] = $_GET['area_id'];
        if(isset($areas[$_GET['area_id']])) $__SETTING['title'] = $areas[$_GET['area_id']] .'优惠信息';
    }
    
    $totalnum = preferentialMdl::getInstance()->getPreferentialCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','`title`','area_id','face_pic','create_time','ip');     
    $datas = preferentialMdl::getInstance()->getPreferentialList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('preferential','main',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('preferential');
    require TEMPLATE_PATH.'preferential.html';
    die;
}
if($_GET['act'] === 'detail'){
    import::getMdl('preferential');
    $id = empty ($_GET['id']) ? show404() : (int)$_GET['id'];    
    $data = preferentialMdl::getInstance()->getPreferential($id);    
    if(empty($data)) show404();
    $__SETTING['title'] = $data['title'];
    import::getMdl('case');
    $cases = caseMdl::getInstance()->getNewCase(5);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('preferentialshow',array('title'=>$data['title']));
    require TEMPLATE_PATH.'preferential_detail.html';
    die;
}
