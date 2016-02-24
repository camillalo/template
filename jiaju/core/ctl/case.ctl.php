<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',50);//分页大小
import::getInt('category');
import::getMdl('case');
import::getInt('recommend');
recommend::getInstance()->init(6);
if($_GET['act'] === 'main'){
    $__SETTING['title'] .= '案例欣赏';
    $url = array();
    $where  = array('is_show'=>1);
    $cateIds = array();
    foreach(category::getInstance()->getRoot($__CATEGORY_TYPE['case']) as $val){
        $_GET['st'.$val['category_id']] = empty($_GET['st'.$val['category_id']]) ?  0 : (int)$_GET['st'.$val['category_id']];
        if(!empty($_GET['st'.$val['category_id']])){
            $url['st'.$val['category_id']] = $_GET['st'.$val['category_id']];
            $cateIds[] = $_GET['st'.$val['category_id']];
           $__SETTING['title'] .= '('.category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$_GET['st'.$val['category_id']]) .')';
        }
    }
    if(!empty($cateIds)) $where['cateIds'] = $cateIds;
    $_GET['type'] = empty($_GET['type']) ?  0 : (int)$_GET['type'];
    if(!empty($_GET['type'])) {
        $url['type'] = $_GET['type'];
        $where['type'] = $_GET['type'];
    }    
    
    $totalnum = caseMdl::getInstance()->getCaseCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('case_id'=>'DESC');
    $col = array('`case_id`','`title`','`face_pic`','description','`pv_num`');
    $datas = caseMdl::getInstance()->getCaseList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('case','main',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('case');
    require TEMPLATE_PATH.'case.html';
    die;
}

if($_GET['act'] === 'detail'){
    $id = empty($_GET['id']) ? show404() : (int)$_GET['id'];
    $caseInfo = caseMdl::getInstance()->getCase($id);
    if(empty($caseInfo)) show404();
    $_GET['space_id'] = $caseInfo['space_id'];
    caseMdl::getInstance()->updatePv($id);
    $pics = json_decode($caseInfo['detail_pics'],true);
    $__SETTING['title']      = $caseInfo['title'];
    $__SETTING['keyword']    = $caseInfo['keywords'];
    $__SETTING['description'] = $caseInfo['description'];
    if(!empty($caseInfo['uid'])){
        import::getMdl('company');
        $company = companyMdl::getInstance()->getCompany($caseInfo['uid']);
        if(empty($company)){
            import::getMdl('designer');
            $designer = designerMdl::getInstance()->getDesignerByUid($caseInfo['uid']);
        }
    }
    $upCase     = caseMdl::getInstance()->getUpCase($id);
    $nextCase    = caseMdl::getInstance()->getNextCase($id);
    $newCases = caseMdl::getInstance()->getNewCase(5);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('caseshow',array('title'=>$caseInfo['title'],'keywords'=>$caseInfo['keywords'],'description'=> $caseInfo['description']));
    require TEMPLATE_PATH.'case_detail.html';
    die;
}