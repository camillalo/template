<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('products');
import::getInt('category');
import::getLib('pscws5');
import::getMdl('keywords');
import::getMdl('productKeywordMaps');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=products&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $cates = empty($_GET['cates']) ? array() : $_GET['cates'];
    
    $category_id = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : 0;
    
    if(empty($category_id)) $category_id = isset($cates[count($cates) - 2]) ? (int) $cates[count($cates) - 2] : 0;
    
    if(isset($_GET['category_id'])) $category_id = (int)$_GET['category_id'];
    if (!empty($category_id)) {
        $lastIds = category::getInstance()->getAllLastChildIds($__CATEGORY_TYPE['products'],$category_id);
        $url.='&category_id=' . $category_id;
        if(!empty($lastIds)){
            $where['last_category_id'] = $lastIds;
        }else{
            $where['category_id'] = $category_id;
        }
    }
    
    $totalnum = productsMdl::getInstance()->getProductsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`product_name`','`category_id`','`brand_id`','`company_id`','`face_pic`','`market_price`','`mall_price`','`is_show`');     
    $datas = productsMdl::getInstance()->getProductsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    import::getMdl('brand');
    import::getMdl('company');
    foreach ($datas as $k => $v) {
        $localArr = category::getInstance()->getCategory($__CATEGORY_TYPE['products'], $v['category_id']);
        $datas[$k]['category_name'] = empty($localArr['category_name']) ? '' : $localArr['category_name'];
        $localArr = brandMdl::getInstance()->getBrand($v['brand_id']);
        $datas[$k]['brand_name'] = empty($localArr['brand_name']) ? '' : $localArr['brand_name']; 
        $datas[$k]['company_name'] = companyMdl::getInstance()->getCompanyName($v['company_id']);
    }
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['products'], $category_id, true);
    logsInt::getInstance()->systemLogs('查看了产品列表');
    require TEMPLATE_PATH.'products/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['product_name'] = empty($_POST['product_name']) ? '': trim(htmlspecialchars($_POST['product_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['product_name'])) errorAlert('产品名称不能为空');
         $info['model'] = empty($_POST['model']) ? '': trim(htmlspecialchars($_POST['model'],ENT_QUOTES,'UTF-8'));
        if(empty($info['model'])) errorAlert('产品型号不能为空');
        $cates = empty($_POST['cates']) ? errorAlert('请选择分类') : $_POST['cates'];
        $info['category_id'] = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : errorAlert('请选择分类');
        if (empty($info['category_id']))
            errorAlert('请选择分类');
        $info['brand_id'] =empty($_POST['brand_id']) ? 0: (int)$_POST['brand_id'];
        if(empty($info['brand_id'])) errorAlert('品牌不能为空');
        $info['face_pic'] = '';
        $info['market_price'] =empty($_POST['market_price']) ? 0: (int)($_POST['market_price']*100);
     
        $info['mall_price'] =empty($_POST['mall_price']) ? 0: (int)($_POST['mall_price']*100);
    
        $info['description'] = empty($_POST['description']) ? '': getValue($_POST['description']);
        if(empty($info['description'])) errorAlert('详情不能为空');
       
        $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $info['detail_pics'] = json_encode($detailPics);
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
          
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $id = productsMdl::getInstance()->addProducts($info);
        if(!$id) errorAlert ('添加失败');
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['product_name']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['product_id'] = $id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            productKeywordMapsMdl::getInstance()->addProductKeywordMaps($mapinfo);
        }
        logsInt::getInstance()->systemLogs('新增了产品',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=products&act=main'");
        die;
    } 

    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['products'], 0);
    require TEMPLATE_PATH.'products/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = productsMdl::getInstance()->getProducts($id);    
    if(empty($data)) errorAlert ('参数出错');
    $data['detail_pics'] = json_decode($data['detail_pics'],true);
    if(empty($data['detail_pics'] )) $data['detail_pics']  = array();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['product_name'] = empty($_POST['product_name']) ? '': trim(htmlspecialchars($_POST['product_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['product_name'])) errorAlert('产品名称不能为空');
         $info['model'] = empty($_POST['model']) ? '': trim(htmlspecialchars($_POST['model'],ENT_QUOTES,'UTF-8'));
        if(empty($info['model'])) errorAlert('产品型号不能为空');
        $cates = empty($_POST['cates']) ? errorAlert('请选择分类') : $_POST['cates'];
        $info['category_id'] = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : errorAlert('请选择分类');
        if (empty($info['category_id']))
            errorAlert('请选择分类');
        $info['brand_id'] =empty($_POST['brand_id']) ? 0: (int)$_POST['brand_id'];
        if(empty($info['brand_id'])) errorAlert('品牌不能为空');
        $info['face_pic'] = $data['face_pic'];
        $info['market_price'] =empty($_POST['market_price']) ? 0: (int)($_POST['market_price']*100);
      
        $info['mall_price'] =empty($_POST['mall_price']) ? 0: (int)($_POST['mall_price'] * 100);
      
        $info['description'] = empty($_POST['description']) ? '': getValue($_POST['description']);
        if(empty($info['description'])) errorAlert('详情不能为空');
         $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $delpics = array();
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];    
                }
            }    
        }  catch (Exception $e){
            if(empty($data['face_pic'])){
                errorAlert($e->getMessage());
            }
        }
        $oldpics = empty($_POST['oldpics']) ? array() : $_POST['oldpics'];
        foreach( $data['detail_pics'] as $k=>$v){
            if(!in_array($v,$oldpics)){
               $delpics[] = $v;
               unset($data['detail_pics'][$k]);
            }
        }
       
        $info['detail_pics'] = json_encode(array_merge($data['detail_pics'] , $detailPics));
        // print_r($info['detail_pics']);
        //die;
        if(false === productsMdl::getInstance()->updateProducts($id,$info)) errorAlert ('添加失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        if($info['product_name'] != $data['product_name']){
            productKeywordMapsMdl::getInstance()->delProductKeywordMapsByProductId($product_id);

            $keywords = PSCWS5::getInstance()->getAllSplitCol($info['product_name']);
            foreach($keywords as $val){
                $mapinfo = array();
                $mapinfo['product_id'] = $id;
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
                if(!$mapinfo['keyword_id']){
                    $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
                }
                productKeywordMapsMdl::getInstance()->addProductKeywordMaps($mapinfo);
            }
        }
        logsInt::getInstance()->systemLogs('修改了产品',$data,$info);
        echoJs("alert('添加成功');parent.location='index.php?ctl=products&act=edit&id=".$id."'");
        die;
    } 
    import::getMdl('brandMap');
    import::getMdl('brand');
    $mapids = brandMapMdl::getInstance()->getAllBrandMap($data['category_id']); 
    $brands  = brandMdl::getInstance()->getBrandsByIds($mapids);
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['products'], $data['category_id'], true);
    require TEMPLATE_PATH.'products/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : $_GET['id'];    
    $ids = array();
    if(is_array($id)){
        foreach($id as $v){
            $ids[] = (int)$v;
        }
    }else{
        $ids [] = (int)$id;
    }
    foreach($ids as $id){  
        $data = productsMdl::getInstance()->getProducts($id);    
        if(empty($data)) errorAlert ('参数出错');
     
        if(false !== productsMdl::getInstance()->delProducts($id)) {
            logsInt::getInstance()->systemLogs('删除了产品',$data,array());
            if(!empty($data['face_pic'])){
                if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
            }
            productKeywordMapsMdl::getInstance()->delProductKeywordMapsByProductId($id);
           
        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=products' : $_GET['back_url'];
     dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}



if($_GET['act'] === 'show'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = productsMdl::getInstance()->getProducts($id);    
    if(empty($data)) errorAlert ('参数出错');
    if((int)$data['is_show'] === 1) errorAlert ('状态不正确'); 
    $info['is_show'] = 1;
    if(false === productsMdl::getInstance()->updateProducts($id,$info)) errorAlert ('更新失败');
    logsInt::getInstance()->systemLogs('显示产品',$data,$info);
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=products' : $_GET['back_url'];
     dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'unshow'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = productsMdl::getInstance()->getProducts($id);    
    if(empty($data)) errorAlert ('参数出错');
    if((int)$data['is_show'] === 0) errorAlert ('状态不正确'); 
    $info['is_show'] = 0;
    if(false === productsMdl::getInstance()->updateProducts($id,$info)) errorAlert ('更新失败');
    logsInt::getInstance()->systemLogs('取消显示产品',$data,$info);
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=products' : $_GET['back_url'];
     dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}