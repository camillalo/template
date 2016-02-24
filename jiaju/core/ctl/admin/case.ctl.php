<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('case');
import::getInt('category');
import::getMdl('caseMap');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=case&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }

    $totalnum = caseMdl::getInstance()->getCaseCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('case_id'=>'DESC');
    
    $col = array('`case_id`','`title`','`type`','`face_pic`','`real_price`','`real_space`','`pv_num`','`is_show`','`create_time`');
    
    $datas = caseMdl::getInstance()->getCaseList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);

    logsInt::getInstance()->systemLogs('查看了案例列表');
    require TEMPLATE_PATH.'case/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $case_id = empty ($_GET['case_id']) ? errorAlert('参数错误') : (int)$_GET['case_id'];    
    $data = caseMdl::getInstance()->getCase($case_id);    
    if(empty($data)) errorAlert ('参数出错');
    $data['detail_pics'] = json_decode($data['detail_pics'],true);
    $data['detail_pics'] = empty($data['detail_pics']) ?  array() : $data['detail_pics'];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

         
        $info['title'] = empty($_POST['title']) ? errorAlert('案例标题不能为空') : trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        $info['type'] =empty($_POST['type']) ? 0 : (int)$_POST['type'];
        $info['real_price'] =empty($_POST['real_price']) ? 0 : (int)($_POST['real_price']*100);
        $info['real_space'] =empty($_POST['real_space']) ? 0 : (int)($_POST['real_space']*100);
        $info['pv_num'] =empty($_POST['pv_num']) ? 0 : (int)$_POST['pv_num'];
        $info['keywords'] = empty($_POST['keywords']) ? '' : trim(htmlspecialchars($_POST['keywords'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '' : trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        $cate_ids = empty($_POST['cate_id']) ? errorAlert('请选择类型') : $_POST['cate_id'];
        $info['face_pic'] = $data['face_pic'];  
        
        $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
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
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];
                }    
            }

        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $oldpics = empty($_POST['oldpics']) ? array() : $_POST['oldpics'];
        foreach( $data['detail_pics'] as $k=>$v){
            if(!in_array($v,$oldpics)){
               $delpics[] = $v;
               unset($data['detail_pics'][$k]);
            }
        }
        $info['detail_pics'] = json_encode(array_merge($data['detail_pics'] , $detailPics));
        if(false === caseMdl::getInstance()->updateCase($case_id,$info)) errorAlert ('更新失败');
        $cateInfo = array();
        foreach($cate_ids as $v){
            $cateInfo[] = array('case_id'=>$case_id,'cate_id'=>(int)$v);
        }
        if(!empty($cateInfo)) {
            caseMapMdl::getInstance()->delCaseMaps($case_id);
            caseMapMdl::getInstance()->addCaseMaps($cateInfo);
        }
        
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('编辑了案例',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=case&act=edit&case_id=".$case_id."'");
        die;
    }
    $cateIds = caseMapMdl::getInstance()->getCaseMapsByCaseId($case_id);
    
    logsInt::getInstance()->systemLogs('打开了编辑案例面板');
    require TEMPLATE_PATH.'case/edit.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        $info['title'] = empty($_POST['title']) ? errorAlert('案例标题不能为空') : trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));   
        $info['type'] =empty($_POST['type']) ? 0 : (int)$_POST['type'];
        $info['real_price'] =empty($_POST['real_price']) ? 0 : (int)($_POST['real_price']*100);
        $info['real_space'] =empty($_POST['real_space']) ? 0 : (int)($_POST['real_space']*100);
        $info['pv_num'] =empty($_POST['pv_num']) ? 0 : (int)$_POST['pv_num'];
        $info['keywords'] = empty($_POST['keywords']) ? '' : trim(htmlspecialchars($_POST['keywords'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '' : trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        $cate_ids = empty($_POST['cate_id']) ? errorAlert('请选择类型') : $_POST['cate_id'];
        
        
        $info['face_pic'] = '';  
        $info['detail_pics'] = '';
        $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
        $info['is_show'] = 1;
        $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $info['detail_pics'] = json_encode($detailPics);
        
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $case_id = caseMdl::getInstance()->addCase($info);
        if(!$case_id) errorAlert ('添加失败');
        $cateInfo = array();
        foreach($cate_ids as $v){
            $cateInfo[] = array('case_id'=>$case_id,'cate_id'=>(int)$v);
        }
        if(!empty($cateInfo)) {
            caseMapMdl::getInstance()->addCaseMaps($cateInfo);
        }
        logsInt::getInstance()->systemLogs('新增了案例',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=case&act=main'");
        die;
    } 

    require TEMPLATE_PATH.'case/add.html';
    die;
}


if($_GET['act'] === 'del'){
    $case_id = empty ($_GET['case_id']) ?  (empty($_GET['id']) ? errorAlert('参数错误') : $_GET['id']): (int)$_GET['case_id'];   
    $ids = array();
    if(is_array($case_id)){
        foreach($case_id as $v){
            $ids[] = (int)$v;
        }
    }else{
        $ids [] = (int)$case_id;
    }
    $data = array();
    foreach($ids as $id){
        $data = caseMdl::getInstance()->getCase($id);    
        if(empty($data)) errorAlert ('参数出错');
        if(false !== caseMdl::getInstance()->delCase($id)) {
            logsInt::getInstance()->systemLogs('删除了案例',$data,array());
            if(!empty($data['face_pic'])){
                if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
            }
            $data['detail_pics'] = json_decode($data['detail_pics'],true);
            foreach($data['detail_pics'] as $v){
                if(file_exists(BASE_PATH.$v)) unlink(BASE_PATH.$v);
            }

        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=case' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'show'){
    $case_id = empty ($_GET['case_id']) ? errorAlert('参数错误') : (int)$_GET['case_id'];    
    $data = caseMdl::getInstance()->getCase($case_id);    
    if(empty($data)) errorAlert ('参数出错');
    if((int)$data['is_show'] === 1) errorAlert ('状态不正确'); 
    $info['is_show'] = 1;
    if(false === caseMdl::getInstance()->updateCase($case_id,$info)) errorAlert ('更新失败');
    logsInt::getInstance()->systemLogs('显示案例操作',$info,array());
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=case' : $_GET['back_url'];
     dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'unshow'){
    $case_id = empty ($_GET['case_id']) ? errorAlert('参数错误') : (int)$_GET['case_id'];    
    $data = caseMdl::getInstance()->getCase($case_id);    
    if(empty($data)) errorAlert ('参数出错');
    if((int)$data['is_show'] === 0) errorAlert ('状态不正确'); 
    $info['is_show'] = 0;
    if(false === caseMdl::getInstance()->updateCase($case_id,$info)) errorAlert ('更新失败');
    logsInt::getInstance()->systemLogs('取消显示案例',$info,array());
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=case' : $_GET['back_url'];
     dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}