<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();

if($_GET['act'] === 'getCategory'){
    import::getInt('category');
    $category_type = empty($_GET['category_type']) ? dieJsonErr('请选择类型') : (int)$_GET['category_type'];
    $parent_id     = empty($_GET['parent_id']) ? 0 : (int)$_GET['parent_id'];
    if($parent_id !==0 ){
        $category_info = category::getInstance()->getCategory($category_type,$parent_id);
        if(empty($category_info)) dieJsonErr ('上级分类不正确');
    }
    $data = category::getInstance()->getChild($category_type,$parent_id);
    dieJsonRight($data);    
    die;
}

if($_GET['act'] === 'getSign'){
    import::getMdl('recommendSign');
    $_GET['group_id'] = empty($_GET['group_id']) ? dieJsonErr('请选择页面') : (int)$_GET['group_id'];
    $data = recommendSignMdl::getInstance()->getRecommendSignByGroupId($_GET['group_id']);
    dieJsonRight($data);    
    die;
}

if($_GET['act'] === 'getBrand'){
    import::getMdl('brandMap');
    import::getMdl('brand');
    $id = empty($_GET['category_id']) ? dieJsonErr('参数错误!') : (int)$_GET['category_id'];
    $mapids = brandMapMdl::getInstance()->getAllBrandMap($id); 
    $brands  = brandMdl::getInstance()->getBrandsByIds($mapids);
    dieJsonRight($brands);
    die;
}


if($_GET['act'] === 'upload'){
    import::getLib('uploadimg');    
    $time = date('YmdH',NOWTIME);
    $watermark = import::getCfg('watermark');
    
    try{
        $return  = uploadImg::getInstance()->upload('Filedata');
        if(!empty($watermark['type'])){
            if($watermark['type'] == 'word'){
                uploadImg::getInstance()->imageWaterMark($return['store_file_name'],'',$watermark['word']);
            }else{
                uploadImg::getInstance()->imageWaterMark($return['store_file_name'],BASE_PATH.$watermark['pic']);
            }
        }
        uploadImg::getInstance()->resizeImage($return['store_file_name'],$return['store_file_name'],800,600);
        $return['store_file_name'] = md5($return['web_file_name'].AUTH_KEY.$time);
        echo join('|||',$return);
    }  catch (Exception $e){
        die('no');
    }
    die;
}
if($_GET['act'] === 'delPic'){
    $time = date('YmdH',NOWTIME);
    $pic = empty($_GET['pic']) ? die('*****') : trim($_GET['pic']);
    $token = empty($_GET['token']) ? die('****') : trim($_GET['token']);
    if(empty($token))die('****');
    if($token !== md5($pic.AUTH_KEY.$time)) die('***');
    $pic = BASE_PATH.'/'.$pic;
    if(file_exists($pic)) unlink ($pic);
    die;
}