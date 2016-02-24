<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('category');
import::getInt('category');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=category&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    
    //必须 
    $_GET['parent_id'] = empty($_GET['parent_id']) ? 0 : (int)$_GET['parent_id'];
    $url.='&parent_id='.  $_GET['parent_id'];
    $where['parent_id'] = $_GET['parent_id'];
    $back_parent_id = 0;
    if($_GET['parent_id']){
        $parent_info = categoryMdl::getInstance()->getCategory($_GET['parent_id']);
        if(!empty($parent_info)) $back_parent_id = $parent_info['parent_id'];
    }
    
    $_GET['category_type'] = empty($_GET['category_type']) ? 1 : (int)$_GET['category_type'];
    if(!empty($_GET['category_type'])){
        $url.='&category_type='.  $_GET['category_type'];
        $where['category_type'] = $_GET['category_type'];
    }
    
    $totalnum = categoryMdl::getInstance()->getCategoryCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('category_id'=>'DESC');
    $col = array('`category_id`','`category_name`','`category_type`','`parent_id`');
    $datas = categoryMdl::getInstance()->getCategoryList($col,$where,$orderby,$begin,PAGE_SIZE);
    $ids = array();
    foreach($datas as $v){
        if(!in_array($v['parent_id'],$ids)) $ids[] = $v['parent_id'];
    }
    $parent_category = categoryMdl::getInstance()->getCategoryPairByCategoryIds($ids);
    
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('打开了分类列表');
    require TEMPLATE_PATH.'category/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $category_id = empty ($_GET['category_id']) ? errorAlert('参数错误') : (int)$_GET['category_id'];    
    $data = categoryMdl::getInstance()->getCategory($category_id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['category_type'] = empty($_POST['category_type']) ? errorAlert('请选择分类类型') : (int)$_POST['category_type'];
        $cates     = empty($_POST['cates']) ? array() : $_POST['cates'];
        if(empty($cates[count($cates)-1])){
            if(isset($cates[count($cates)-2])){
                $info['parent_id'] = (int)$cates[count($cates)-2];
            }else{
                $info['parent_id'] =  0;
            }
        }else{
            $info['parent_id'] = (int)$cates[count($cates)-1];
        }
        if($info['parent_id'] !==0 ){
            $category_info = categoryMdl::getInstance()->getCategory($info['parent_id']);
            if(empty($category_info)) errorAlert ('上级分类不正确');
            if((int)$category_info['category_type'] !== $info['category_type']) errorAlert ('分类和类型不一致');    
        }
        $info['category_name'] = empty($_POST['category_name']) ? errorAlert('参数出错') : trim(htmlspecialchars($_POST['category_name'],ENT_QUOTES,'UTF-8'));    
        if(false === categoryMdl::getInstance()->updateCategory($category_id,$info)) errorAlert ('更新失败');
        category::getInstance()->put();
        logsInt::getInstance()->systemLogs('修改了分类',$data,$info);
        errorAlert('操作成功');
        die;
    }
    logsInt::getInstance()->systemLogs('打开了修改分类面板');
    $select = category::getInstance()->getSelect($data['category_type'],$category_id);
    require TEMPLATE_PATH.'category/edit.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['category_type'] = empty($_POST['category_type']) ? errorAlert('请选择分类类型') : (int)$_POST['category_type'];
        $parent_id     = empty($_POST['parent_id']) ? array() : $_POST['parent_id'];
        if(empty($parent_id[count($parent_id)-1])){
            if(isset($parent_id[count($parent_id)-2])){
                $info['parent_id'] = (int)$parent_id[count($parent_id)-2];
            }else{
                $info['parent_id'] =  0;
            }
        }else{
            $info['parent_id'] = (int)$parent_id[count($parent_id)-1];
        }
        if($info['parent_id'] !==0 ){
            $category_info = categoryMdl::getInstance()->getCategory($info['parent_id']);
            if(empty($category_info)) errorAlert ('上级分类不正确');
            if((int)$category_info['category_type'] !== $info['category_type']) errorAlert ('分类和类型不一致');    
        }
        $info['category_name'] = empty($_POST['category_name']) ? errorAlert('分类名称不能为空') : trim(htmlspecialchars($_POST['category_name'],ENT_QUOTES,'UTF-8'));        
        if(!categoryMdl::getInstance()->addCategory($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了分类',$info,array());
        category::getInstance()->put();
        echoJs("alert('添加成功');parent.location='index.php?ctl=category&act=add'");
        die;
    } 
    require TEMPLATE_PATH.'category/add.html';
    die;
}


if($_GET['act'] === 'del'){
    $category_id = empty ($_GET['category_id']) ? errorAlert('参数错误') : (int)$_GET['category_id'];    
    $data = categoryMdl::getInstance()->getCategory($category_id);    
    if(empty($data)) errorAlert ('参数出错');
    if(categoryMdl::getInstance()->getCountCategoryByParentId($category_id)) errorAlert ('该分类下还有其他分类');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=category' : $_GET['back_url'];
    if(false !== categoryMdl::getInstance()->delCategory($category_id)) {
        logsInt::getInstance()->systemLogs('删除了分类',$data,array());
        category::getInstance()->put();
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

