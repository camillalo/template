<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('brand');
import::getInt('category');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=brand&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = brandMdl::getInstance()->getBrandCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('brand_id'=>'DESC');    $col = array('`brand_id`','`brand_name`','`brand_pic`');     
    $datas = brandMdl::getInstance()->getBrandList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['products'], 0);
    logsInt::getInstance()->systemLogs('查看了品牌列表');
    require TEMPLATE_PATH.'brand/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['brand_name'] = empty($_POST['brand_name']) ? '': trim(htmlspecialchars($_POST['brand_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['brand_name'])) errorAlert('品牌名称不能为空');
        $info['brand_pic'] = '';

        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['brand_pic']['tmp_name'])){ 
            $brand_pic = uploadImg::getInstance()->upload('brand_pic');
            if(!empty($brand_pic['web_file_name'])) $info['brand_pic'] = $brand_pic['web_file_name'];
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!brandMdl::getInstance()->addBrand($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了品牌',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=brand&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'brand/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $brand_id = empty ($_GET['brand_id']) ? errorAlert('参数错误') : (int)$_GET['brand_id'];    
    $data = brandMdl::getInstance()->getBrand($brand_id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['brand_name'] = empty($_POST['brand_name']) ? '': trim(htmlspecialchars($_POST['brand_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['brand_name'])) errorAlert('品牌名称不能为空');
        $info['brand_pic'] = $data['brand_pic'];
 
        $delpics = array();
        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['brand_pic']['tmp_name'])){ 
                $brand_pic = uploadImg::getInstance()->upload('brand_pic');
                if(!empty($brand_pic['web_file_name'])) {
                    $info['brand_pic'] = $brand_pic['web_file_name'];
                    $delpics[] = $data['brand_pic'];    
                }
           } 
            
        }  catch (Exception $e){
            if(empty($data['brand_pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === brandMdl::getInstance()->updateBrand($brand_id,$info)) errorAlert ('添加失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('修改了品牌',$data,$info);
        echoJs("alert('添加成功');parent.location='index.php?ctl=brand&act=edit&brand_id=".$brand_id."'");
        die;
    } 

    require TEMPLATE_PATH.'brand/edit.html';
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
        $data = brandMdl::getInstance()->getBrand($id);    
        if(empty($data)) errorAlert ('参数出错');     
        if(false !== brandMdl::getInstance()->delBrand($id)) {
            logsInt::getInstance()->systemLogs('删除了品牌',$data,array());
            if(!empty($data['brand_pic'])){
                if(file_exists(BASE_PATH.$data['brand_pic'])) unlink(BASE_PATH.$data['brand_pic']);
            }
        }
    }
     $back_url = empty($_GET['back_url']) ? 'index.php?ctl=brand' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'delMap'){
    $brand_id = empty ($_GET['brand_id']) ? dieJsonErr('参数错误') : (int)$_GET['brand_id'];   
    import::getMdl('brandMap');
    if(!brandMapMdl::getInstance()->delBrandMapByBrandId($brand_id)) dieJsonErr ('操作失败');
    logsInt::getInstance()->systemLogs('删除了品牌绑定');
    dieJsonRight('操作成功');
    die;
}

if($_GET['act'] === 'getMap'){
    $brand_id = empty ($_GET['brand_id']) ? dieJsonErr('参数错误') : (int)$_GET['brand_id'];   
    $cates = empty($_POST['cates']) ? array() : $_POST['cates'];
    logsInt::getInstance()->systemLogs('品牌绑定分类');
    import::getMdl('brandMap');
    $categorys = brandMapMdl::getInstance()->getAllCategoryMap($brand_id);
    //print_r($categorys);
    if(!empty($cates)){
        $cate_id = 0;
        foreach($cates as $v){
            if(!empty($v)) $cate_id = $v;
        }
        
        if(!empty($cate_id)){
            $cateids = category::getInstance()->getAllLastChildIds($__CATEGORY_TYPE['products'],$cate_id);
            $info = array();
            if(empty($cateids) && !in_array($cate_id,$categorys)){
                 $info[] = array(
                     'brand_id' => $brand_id,
                     'category_id' => $cate_id
                 );
                 $categorys[] = $cate_id;
            }else{
                
                foreach($cateids as $v){
                    if(!in_array($v,$categorys)){
                        $categorys[] = $v;
                        $info[] = array(
                            'brand_id' => $brand_id,
                            'category_id' => $v
                        );
                    }
                }
            }
            if(!empty($info)){
                brandMapMdl::getInstance()->addBrandMaps($info);
            }
        }
        
    }
   // $categorys = brandMapMdl::getInstance()->getAllCategoryMap($brand_id);
    
    $local = array();
    
    foreach($categorys as $val){
        $cate = category::getInstance()->getCategory($__CATEGORY_TYPE['products'],$val);
        $local[] = $cate['category_name'];
    }
    dieJsonRight(join(',',$local));
    die;
}