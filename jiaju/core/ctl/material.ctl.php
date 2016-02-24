<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getInt('recommend');
recommend::getInstance()->init(2);
if($_GET['act'] === 'main'){
    $__SETTING['title'] = '装饰材料公司';
    define('PAGE_SIZE',6);//分页大小
    import::getMdl('company');
    $url = array();
    $where = array('type'=>$__USER_TYPE['material']);
    $_GET['area_id'] = empty($_GET['area_id']) ? 0 : (int)$_GET['area_id'];
    if(!empty($_GET['area_id'])){
        $url ['area_id'] =   $_GET['area_id'];
        $where ['area_id'] =   $_GET['area_id'];
    }
    $_GET['industry_id'] = empty($_GET['industry_id']) ? 0 : (int)$_GET['industry_id'];
    if(!empty($_GET['industry_id'])){
        $url ['industry_id'] =   $_GET['industry_id'];
        $where ['industry_id'] =   $_GET['industry_id'];
    }
    $_GET['scale_id'] = empty($_GET['scale_id']) ? 0 : (int)$_GET['scale_id'];
    if(!empty($_GET['scale_id'])){
        $url ['scale_id'] =   $_GET['scale_id'];
        $where ['scale_id'] =   $_GET['scale_id'];
    }
    
    $totalnum = companyMdl::getInstance()->getCompanyCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=> 'DESC','uid'=>'DESC');  
    $col = array('`uid`','`logo`','`qq_id`','`addr_id`','`orderby`','`pv`','`company_name`','`founding_year`','`comment_num`','`average_score`');     
    $datas = companyMdl::getInstance()->getCompanyList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage( mkUrl::linkTo('material','main',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);

    $areas = areaInt::getInstance()->getAreas();
    $industrys = category::getInstance()->getChildCol($__CATEGORY_TYPE['company'],$__COMPANY_CATEGORY_ROOT['industry']);
    $scales  = category::getInstance()->getChildCol($__CATEGORY_TYPE['company'],$__COMPANY_CATEGORY_ROOT['scale']);
    import::getMdl('companyAddrs');
    import::getMdl('products');
    $ids = array();
    foreach($datas as $k=> $val){ //因为 分页就10条 就没有批量查询了 单条的主键查询性能上差异不会太大
        if(empty($val['logo'])) $datas[$k]['logo'] = 'statics/images/132_67.jpg';   
        $companyAddr = companyAddrsMdl::getInstance()->getCompanyAddrs($val['addr_id']);
        if(empty($companyAddr)) $companyAddr = companyAddrsMdl::getInstance ()->getCompanyAddrByCompanyid($val['uid']);
         $datas[$k]['addr'] = empty($companyAddr['addr']) ? '未填写' : $companyAddr['addr'];
         $datas[$k]['tel'] = empty($companyAddr['tel']) ? '未填写' : $companyAddr['tel'];
         $datas[$k]['products'] = productsMdl::getInstance()->getProuctsByCompanyId($val['uid'],4);
         $ids[] = $val['uid'];
    }
    $productsCount = productsMdl::getInstance()->getProductsCountPair($ids);
    $newCompanyProducts = productsMdl::getInstance()->getNewCompanyProucts(20);
    
    import::getMdl('security');
    $security = securityMdl::getInstance()->getSecuritysByids($ids);
    $securitydatas =  array();
    foreach($security as $v){
        $securitydatas[$v['uid']] = $v;
        $securitydatas[$v['uid']]['is_security'] =   $v['money1'] + $v['money2'];  
    }
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('material');
    require TEMPLATE_PATH.'material.html';
    die;
}