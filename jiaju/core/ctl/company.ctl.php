<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
$company_id = empty($_GET['id']) ? show404() : (int)$_GET['id'];
import::getMdl('company');
import::getMdl('companyDianping');
import::getMdl('users');
$company = companyMdl::getInstance()->getCompany($company_id);
if(empty($company)) show404 ();

import::getInt('seo');
$__SETTING = seoInt::getInstance()->load('gsshop',array('companyname'=>$company['company_name']));

if(empty($company['logo']))$company['logo'] = 'statics/images/132_67.jpg';
$templateId = empty($company['template_id']) ? 0 : (int)$company['template_id'];

define('COMPANY_TEMPLATE',BASE_PATH.'themes/company/style'.$templateId.'/');

$user    = usersMdl::getInstance()->getUsers($company_id);
if(empty($user)) show404 ();

//替换一下 VIP 等级
if($user['day'] < NOWTIME && empty($user['num']) && empty($user['gold']) ){
    if(!empty($user['rank_id'])) usersMdl::getInstance ()->updateUsers($company_id,array('rank_id'=>0));
}
    


$is_authentication = usersMdl::getInstance()->checkIsAuthentication($company_id);
$dianping = companyDianpingMdl::getInstance()->getCompanyDianpingAverageScore($company_id);
if(!empty($company['addr_id'])){
    import::getMdl('companyAddrs');
    $defaultCompanyAddr = companyAddrsMdl::getInstance()->getCompanyAddrs($company['addr_id']); 
}

import::getMdl('companyQqs');
$qqs = companyQqsMdl::getInstance()->getAllCompanyQqs($company_id);






if($_GET['act'] !== 'dianpingSave'){
     if($user['day'] > NOWTIME || !empty($user['num']) || !empty($user['gold']) ){ 
        import::getMdl('ranks');
        $rank = ranksMdl::getInstance()->getRanks($user['rank_id']);
     }
     $__SETTING['title'] = $company['company_name'];
}
//公共部分结束


if($_GET['act'] === 'main'){
    companyMdl::getInstance()->updateCompanyPv($company_id);
    import::getMdl('case');
    $anli = caseMdl::getInstance()->getCaseList(null,array('uid'=>$company_id),null,0,10);
    $newDianping = companyDianpingMdl::getInstance()->getCompanyDianpingNewListByCompanyId($company_id,3);
    import::getMdl('designer');
    $designer = designerMdl::getInstance()->getDesignersByUid($company_id,10);
    import::getMdl('products');
    $orderby = array('id'=>'DESC');    
    $col = array('`id`','`product_name`','`face_pic`','`mall_price`');     
    $products = productsMdl::getInstance()->getProductsList($col,array('company_id'=>$company_id),$orderby,0,10);
    
    import::getMdl('companyPics');
    $companyPics = companyPicsMdl::getInstance()->getCompanyPicsByUidType($company_id,$__COMPANY_PIC_TYPE['pics'],3);
    
    require COMPANY_TEMPLATE.'index.html';
    die;
}

if($_GET['act'] === 'dianping'){
    define('PAGE_SIZE',10);
    $__SETTING['title'] .='(点评)';
    
    $where  = array(
        'company_id' => $company_id,
        'is_show' => 1   
    );
    $totalnum = companyDianpingMdl::getInstance()->getCompanyDianpingCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');  
    $col = array('a.*');     
    $datas = companyDianpingMdl::getInstance()->getCompanyDianpingList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','dianping',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    
    require COMPANY_TEMPLATE.'dianping.html';
    die;
}

if($_GET['act'] === 'dianpingSave'){
    $info['uid'] =  getUid();
    if(empty($info['uid']))        dieJsonErr('请登陆后操作');
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('dianping',$info['uid'])) dieJsonErr ('您没有权限评论');
    
    $info['company_id'] = $company_id;
    if(companyDianpingMdl::getInstance()->getCompanyDianpingCount(array('uid'=>$info['uid'],'company_id'=>$info['company_id']))) dieJsonErr('已经评论过了');
    $info['process'] =empty($_POST['process']) ? 0: (int)$_POST['process'];
    if(empty($info['process'])) dieJsonErr('工艺不能为空');
    if(!isset($__DIANPING_MEANS[$info['process']])) dieJsonErr ('参数错误');
    $info['service'] =empty($_POST['service']) ? 0: (int)$_POST['service'];
    if(empty($info['service'])) dieJsonErr('服务不能为空');
    if(!isset($__DIANPING_MEANS[$info['service']])) dieJsonErr ('参数错误');
    $info['design'] =empty($_POST['design']) ? 0: (int)$_POST['design'];
    if(empty($info['design'])) dieJsonErr('设计不能为空');
    if(!isset($__DIANPING_MEANS[$info['design']])) dieJsonErr ('参数错误');
    $info['sales'] =empty($_POST['sales']) ? 0: (int)$_POST['sales'];
    if(empty($info['sales'])) dieJsonErr('售后不能为空');
    if(!isset($__DIANPING_MEANS[$info['sales']])) dieJsonErr ('参数错误');
    
    $info['dianping'] = empty($_POST['dianping']) ? '': trim(htmlspecialchars($_POST['dianping'],ENT_QUOTES,'UTF-8'));
    if(empty($info['dianping'])) dieJsonErr('评价不能为空');
    $info['project'] = empty($_POST['project']) ? '': trim(htmlspecialchars($_POST['project'],ENT_QUOTES,'UTF-8'));
    if(empty($info['project'])) dieJsonErr('装修项目不能为空');
    $info['contact'] = empty($_POST['contact']) ? '': trim(htmlspecialchars($_POST['contact'],ENT_QUOTES,'UTF-8'));
    if(empty($info['contact'])) dieJsonErr('联系方式不能为空');
    $info['realname'] = empty($_POST['realname']) ? '': trim(htmlspecialchars($_POST['realname'],ENT_QUOTES,'UTF-8'));
    if(empty($info['realname'])) dieJsonErr('称呼不能为空');
    $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
    $info['is_show'] = (int)authorityInt::getInstance()->isShow();
    if(!companyDianpingMdl::getInstance()->addCompanyDianping($info)) dieJsonErr ('添加失败');
    $data = companyDianpingMdl::getInstance()->getCompanyDianpingAverageScore($company_id);
    if(empty($data)){
        $update['comment_num'] = 0;
        $update['average_score'] = 50;
    }else{
        $update['comment_num'] = $data['num'];
        $update['average_score'] = (int)(($data['p'] + $data['s'] + $data['d'] + $data['sa'] )/4 * 10);
    }
    companyMdl::getInstance()->updateCompany($company_id,$update);
    dieJsonRight('评价成功');
    die;

}


if($_GET['act'] === 'case'){
    define('PAGE_SIZE',8);
    import::getInt('category');
    import::getMdl('case');
    $__SETTING['title'].='案例展示';
    $where  = array('is_show'=>1,'uid'=>$company_id);
    $url['id'] = $company_id;
    $_GET['space_id'] = empty($_GET['space_id']) ?  0 : (int)$_GET['space_id'];
    if(!empty($_GET['space_id'])) {
        $url['space_id'] = $_GET['space_id'];
        $where['space_id'] = $_GET['space_id'];
        $__SETTING['title'] .= '('.category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$_GET['space_id']) .')';
    }
    $totalnum = caseMdl::getInstance()->getCaseCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('case_id'=>'DESC');
    $col = array('`case_id`','`title`','`face_pic`','`real_space`','`price_id`','detail_pics');
    $datas = caseMdl::getInstance()->getCaseList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','case',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    $spaces = category::getInstance()->getChild($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['space']);
    require COMPANY_TEMPLATE.'case.html';
    die;
}

if($_GET['act'] === 'about'){
    $__SETTING['title'] .='(公司介绍)';
    import::getInt('category');
    import::getMdl('companyArea');
    $areas = companyAreaMdl::getInstance()->getcompanyAreaNameCol($company_id);
    if((int)$company['type'] === $__USER_TYPE['company']){ 
        import::getMdl('companyProject');
        $projects = companyProjectMdl::getInstance()->getcompanyProjectcCol($company_id);
        $showProjects = array();
        foreach($projects as $v){
            $showProjects[] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['company'],$v);
        }
    }else{
        import::getMdl('companyIndustry');
        $projects = companyIndustryMdl::getInstance()->getcompanyIndustryCol($company_id);
        $showProjects = array();
        foreach($projects as $v){
            $showProjects[] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['company'],$v);
        }
    }

  
    import::getMdl('security');
    $security = securityMdl::getInstance()->getSecurity($company_id);
    require COMPANY_TEMPLATE.'about.html';
    die;
}

if($_GET['act'] === 'photo'){
    $__SETTING['title'] .='(公司图片)';
    define('PAGE_SIZE',10);
    import::getMdl('companyPics');
    $where  = array('uid'=>$company_id,'type'=>$__COMPANY_PIC_TYPE['pics']);
    $totalnum = companyPicsMdl::getInstance()->getCompanyPicsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('id'=>'DESC');  
    $col = array('`title`','`pic`');     
    $datas = companyPicsMdl::getInstance()->getCompanyPicsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','photo',array('id'=>$company_id,'page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require COMPANY_TEMPLATE.'photo.html';
    die;
}

if($_GET['act'] === 'credit'){
    $__SETTING['title'] .='(荣誉资质)';
    define('PAGE_SIZE',10);
    import::getMdl('companyPics');
    $where  = array('uid'=>$company_id,'type'=>$__COMPANY_PIC_TYPE['credit']);
    $totalnum = companyPicsMdl::getInstance()->getCompanyPicsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('id'=>'DESC');  
    $col = array('`title`','`pic`');     
    $datas = companyPicsMdl::getInstance()->getCompanyPicsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','credit',array('id'=>$company_id,'page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require COMPANY_TEMPLATE.'credit.html';
    die;
}

if($_GET['act'] === 'contact'){
    $__SETTING['title'] .='(联系我们)';
    import::getMdl('companyAddrs');
    $addrs = companyAddrsMdl::getInstance()->getAllCompanyAddrs($company_id);
    require COMPANY_TEMPLATE.'contact.html';
    die;
}


if($_GET['act'] === 'design'){
    $__SETTING['title'] .='(设计师)';
    define('PAGE_SIZE',10);
    import::getMdl('designer');
    $url = array(); 
    $where = array('uid'=>$company_id);
    $totalnum = designerMdl::getInstance()->getDesignerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','`face_pic`','`area_id`','`name`','`position`','`school`','`from_time`','`style`');     
    $datas = designerMdl::getInstance()->getDesignerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','design',array('id'=>$company_id,'page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require COMPANY_TEMPLATE.'team.html';
    die;
}


if($_GET['act'] === 'products'){
    $__SETTING['title'] .='(产品目录)';
    define('PAGE_SIZE',10);
    import::getMdl('products');
    $where  = array('company_id'=>$company_id);
    $totalnum = productsMdl::getInstance()->getProductsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    
    $col = array('`id`','`product_name`','`face_pic`','`market_price`','`mall_price`');     
    $datas = productsMdl::getInstance()->getProductsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','products',array('id'=>$company_id,'page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require COMPANY_TEMPLATE.'products.html';
    die;
}

if($_GET['act'] === 'site'){
     $__SETTING['title'] .='(在建工地)';
     define('PAGE_SIZE',10);
     import::getMdl('buildingSite');
    import::getInt('category');
    $url = array('id'=>$company_id); 
   
    $where  = array('company_id'=>$company_id);

    $totalnum = buildingSiteMdl::getInstance()->getBuildingSiteCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array();  
    $datas = buildingSiteMdl::getInstance()->getBuildingSiteList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('company','site',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
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
    require COMPANY_TEMPLATE.'site.html';
    die;
}


if($_GET['act'] === 'quantityRoom'){
     import::getMdl('quantityRoom');
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = getUid();
        if(empty($info['uid'])) dieJs ('parent.ajaxLogin();');
        $info['company_id'] = $company_id;
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        if(!isMobile($info['tel']) && !isPhone($info['tel'])) errorAlert ('联系方式请填写手机或者固定电话');
        $info['date'] = empty($_POST['date']) ? '': trim(htmlspecialchars($_POST['date'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(!quantityRoomMdl::getInstance()->addQuantityRoom($info)) errorAlert ('操作失败');
        
        import::getInt('sms');
        if(!empty($user['mobile'])){
            smsInt::getInstance()->send('liangf',array($user['mobile']),array('name'=>$info['name'],'company'=>$company['company_name'],'tel'=> $info['tel'],'site_name'=>$__SETTING['site_name']));
        }
        
        echoJs("alert('预约成功！该公司客服再收到你们的预约后会第一时间和您取得联系！您还可以预约其企业为您量房！');parent.location='".mkUrl::linkTo('company','main',array('id'=>$company_id))."'");
        die;
    } 
    
    require COMPANY_TEMPLATE.'quantityRoom.html';
    die;
}


if ($_GET['act'] === 'siteDetail' ){
    import::getMdl('buildingSite');  
    $site_id = empty($_GET['sid']) ? show404() : (int)$_GET['sid'];
    $siteInfo = buildingSiteMdl::getInstance()->getBuildingSite($site_id);
    if(empty($siteInfo)) show404 ();
    import::getMdl('designer');
    $siteInfo['designer_name'] =  designerMdl::getInstance()->getDesignerName($siteInfo['designer_id']);
    
    import::getMdl('buildingSiteStatus');
    $datas = buildingSiteStatusMdl::getInstance()->getAllSiteStatusBySid($siteInfo['id']);
    buildingSiteMdl::getInstance()->updatePv($site_id);
    require COMPANY_TEMPLATE.'siteDetail.html';
    die;
}
