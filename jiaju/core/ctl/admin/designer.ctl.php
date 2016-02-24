<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('designer');
import::getMdl('area');
import::getMdl('company');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=designer&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = designerMdl::getInstance()->getDesignerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby' => 'desc','id'=>'DESC');    
    $col = array('`id`','`uid`','is_gold','`face_pic`','`area_id`','`name`','`position`','`school`','`from_time`','`style`');     
    $datas = designerMdl::getInstance()->getDesignerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    import::getMdl('users');
    foreach($datas as $k=>$val){
        $area = areaMdl::getInstance()->getArea($val['area_id']);
        $datas[$k]['area_name'] = isset($area['area_name']) ? $area['area_name'] : '' ;

        $datas[$k]['company_name'] = companyMdl::getInstance()->getCompanyName($val['uid']);
        $datas[$k]['is_authentication'] = usersMdl::getInstance()->checkIsAuthentication($val['uid']);
        if(empty($datas[$k]['company_name'])) $datas[$k]['company_name'] = '自由职业';
    }
    logsInt::getInstance()->systemLogs('查看了设计师列表');
    require TEMPLATE_PATH.'designer/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
   
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名字不能为空');
        $info['position'] = empty($_POST['position']) ? '': trim(htmlspecialchars($_POST['position'],ENT_QUOTES,'UTF-8'));
        if(empty($info['position'])) errorAlert('职位不能为空');
        $info['school'] = empty($_POST['school']) ? '': trim(htmlspecialchars($_POST['school'],ENT_QUOTES,'UTF-8'));
        if(empty($info['school'])) errorAlert('毕业院校不能为空');
        $info['from_time'] =empty($_POST['from_time']) ? 0: (int)$_POST['from_time'];
        if(empty($info['from_time'])) errorAlert('工作经验不能为空');
        $info['style'] = empty($_POST['style']) ? '': trim(htmlspecialchars($_POST['style'],ENT_QUOTES,'UTF-8'));
        if(empty($info['style'])) errorAlert('设计风格不能为空');
        $info['about'] = empty($_POST['about']) ? '': trim(htmlspecialchars($_POST['about'],ENT_QUOTES,'UTF-8'));
        if(empty($info['about'])) errorAlert('自我介绍不能为空');
         try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
           uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],127,180);
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!designerMdl::getInstance()->addDesigner($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了设计师',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=designer&act=add'");
        die;
    } 
    $areas = areaMdl::getInstance()->getAreaPair();
    require TEMPLATE_PATH.'designer/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = designerMdl::getInstance()->getDesigner($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('用户ID不能为空');
    
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名字不能为空');
        $info['position'] = empty($_POST['position']) ? '': trim(htmlspecialchars($_POST['position'],ENT_QUOTES,'UTF-8'));
        if(empty($info['position'])) errorAlert('职位不能为空');
        $info['school'] = empty($_POST['school']) ? '': trim(htmlspecialchars($_POST['school'],ENT_QUOTES,'UTF-8'));
        if(empty($info['school'])) errorAlert('毕业院校不能为空');
        $info['from_time'] =empty($_POST['from_time']) ? 0: (int)$_POST['from_time'];
        if(empty($info['from_time'])) errorAlert('工作经验不能为空');
        $info['style'] = empty($_POST['style']) ? '': trim(htmlspecialchars($_POST['style'],ENT_QUOTES,'UTF-8'));
        if(empty($info['style'])) errorAlert('设计风格不能为空');
        $info['about'] = empty($_POST['about']) ? '': trim(htmlspecialchars($_POST['about'],ENT_QUOTES,'UTF-8'));
        if(empty($info['about'])) errorAlert('自我介绍不能为空');
        $info['face_pic'] = $data['face_pic'];
        $delpics = array();
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],127,180);
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];
                }    
            }
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        if(false === designerMdl::getInstance()->updateDesigner($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('编辑了设计师',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=designer&act=edit&id=".$id."'");
        die;
    } 

    $areas = areaMdl::getInstance()->getAreaPair();
    require TEMPLATE_PATH.'designer/edit.html';
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
        $data = designerMdl::getInstance()->getDesigner($id);    
        if(empty($data)) errorAlert ('参数出错');
        if(false !== designerMdl::getInstance()->delDesigner($id)) {
           logsInt::getInstance()->systemLogs('删除了设计师',$data,array());
           if(!empty($data['face_pic'])){
                if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
            }
           
        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=designer' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'authentication'){
     $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = designerMdl::getInstance()->getDesigner($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=designer' : $_GET['back_url'];
    import::getMdl('users');
    $is_authentication = usersMdl::getInstance()->checkIsAuthentication($data['uid']);
    $info['is_authentication']  = $is_authentication ? 0 : 1;
    $info['uid'] = $data['uid'];
    if(false !== usersMdl::getInstance()->replaceUsersEx($info)){
        logsInt::getInstance()->systemLogs('编辑设计师认证',$data,$info);
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    
    die;
}

if($_GET['act'] === 'gold'){
     $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = designerMdl::getInstance()->getDesigner($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=designer' : $_GET['back_url'];
    if(designerMdl::getInstance()->updateDesigner($id,array('is_gold' => $data['is_gold'] ? 0 : 1))) {
        logsInt::getInstance()->systemLogs('编辑了设计师金牌',$data,array('is_gold' => $data['is_gold'] ? 0 : 1));
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    
    die;
}


if($_GET['act'] === 'promotion'){
   $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
   $data = designerMdl::getInstance()->getDesigner($id);    
   if(empty($data)) errorAlert ('参数出错');
   if($_SERVER['REQUEST_METHOD'] === 'POST'){
       $info['orderby'] = empty($_POST['orderby']) ? 0 : (int)$_POST['orderby'];
        if(false !== designerMdl::getInstance()->updateDesigner($id,$info)){
            logsInt::getInstance()->systemLogs('编辑了设计师排名',$data,$info);
             errorAlert('操作成功');
        }
        errorAlert('操作失败');
       die;
   }  
   require  TEMPLATE_PATH.'designer/orderby.html';
   die;
}