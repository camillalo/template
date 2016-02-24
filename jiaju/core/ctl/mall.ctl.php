<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',16);//分页大小
import::getInt('category');
import::getMdl('products');
import::getInt('recommend');
recommend::getInstance()->init(5);
if($_GET['act'] === 'main'){
    $__SETTING['title'] .='商城';
    import::getMdl('brandMap');
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('mall');
    require TEMPLATE_PATH.'mall.html';
    die;
}

if($_GET['act'] === 'list'){

    $__SETTING['title'] .='商城';
    import::getMdl('brandMap');
    import::getMdl('brand');
    $category_id = empty($_GET['category']) ? 0 : (int)$_GET['category'];
    $where  = array('is_show'=>1);
    
    $url = array(
        'category'=>$category_id,
    );
    if(!empty($category_id)){
        $childCates = category::getInstance()->getAllLastChildIds($__CATEGORY_TYPE['products'],$category_id);
        if(empty($childCates)){
            $where['category_id'] = $category_id;
        }else{
            $where['last_category_id'] = $childCates;
        }
    }

    
    $brand_id = empty($_GET['brand']) ? 0 : (int)$_GET['brand'];
    if(!empty($brand_id)){
        $url ['brand'] = $brand_id;
        $where['brand_id'] = $brand_id;
    }
    
    $brands = brandMapMdl::getInstance()->getAllBrandsByMap($category_id); 

    $totalnum = productsMdl::getInstance()->getProductsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    
    $col = array('`id`','`product_name`','`category_id`','`model`','`company_id`','`face_pic`','`market_price`','`mall_price`');     
    $datas = productsMdl::getInstance()->getProductsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('mall','list',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
   import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('mall');
    require TEMPLATE_PATH.'mall_list.html';
    die;
}

if($_GET['act'] === 'detail'){
    $id = empty($_GET['id']) ? show404() : (int)$_GET['id'];
    $data = productsMdl::getInstance()->getProducts($id);

    if(empty($data)) show404 ();
    $__SETTING['title'] = $data['product_name'];
    $data['pics'] = json_decode($data['detail_pics'],true);
    if(empty($data['pics'])) $data['pics'] = array();
    if(!empty($data['company_id'])){
        import::getMdl('companyQqs');
        $qqs = companyQqsMdl::getInstance()->getAllCompanyQqs($data['company_id']);
        import::getMdl('companyAddrs');
        $addrs = companyAddrsMdl::getInstance()->getAllCompanyAddrs($data['company_id']);
        import::getMdl('company');
        $company  = companyMdl::getInstance()->getCompany($data['company_id']);
    }
    if(!empty($data['category_id'])){
        $loves = productsMdl::getInstance()->getProductsByCategoryId($data['category_id'],6); 
    }
    import::getMdl('brand');
    import::getInt('category');
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('mallshow',array('productname'=> $data['product_name'] ,'description'=> $data['description']));
    require TEMPLATE_PATH.'mall_detail.html';
    die;
}

if($_GET['act'] === 'application'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        import::getMdl('demand');
        $info['uid'] = getUid(); 
        if(empty($info['uid'])) dieJs("parent.ajaxLogin();");
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('联系人不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['demand'] = empty($_POST['demand']) ? '': trim(htmlspecialchars($_POST['demand'],ENT_QUOTES,'UTF-8'));
        if(empty($info['demand'])) errorAlert('需求不能为空');
        $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
        
        if(!demandMdl::getInstance()->addDemand($info)) errorAlert ('很感谢您的申请！稍后我们会有专业的客服联系您！');
        echoJs("alert('添加成功');parent.location='".  mkUrl::linkTo('index')."'");
        die;
    } 
    $__SETTING['title'] .='商城申请';
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('mall');
    require TEMPLATE_PATH.'mall_application.html';
    die;
}