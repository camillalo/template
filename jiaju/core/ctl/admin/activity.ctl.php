<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('activity');
import::getMdl('area');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=activity&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = activityMdl::getInstance()->getActivityCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','area_id','`title`','`type`','`face_pic`','`reg_time`','`bg_time`','`end_time`','`tel`');     
    $datas = activityMdl::getInstance()->getActivityList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    foreach($datas as $k=>$val){
        $area = areaMdl::getInstance()->getArea($val['area_id']);
        $datas[$k]['area_name'] = isset($area['area_name']) ? $area['area_name'] : '' ;
    }
    logsInt::getInstance()->systemLogs('查看了团购列表');
    require TEMPLATE_PATH.'activity/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('活动名称不能为空');
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['face_pic'] = '';
        $info['lng'] = empty($_POST['lng']) ? 0 : (int)($_POST['lng']*100000);//存整数到数据库
        $info['lat'] = empty($_POST['lat']) ? 0 : (int)($_POST['lat']*100000);
        $info['sj'] = empty($_POST['sj']) ? '': trim(htmlspecialchars($_POST['sj'],ENT_QUOTES,'UTF-8'));
        $info['reg_time'] = empty($_POST['reg_time']) ? '': trim(htmlspecialchars($_POST['reg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['reg_time'])) errorAlert('报名截止日期不能为空');
        $info['bg_time'] = empty($_POST['bg_time']) ? '': trim(htmlspecialchars($_POST['bg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['bg_time'])) errorAlert('开始日期不能为空');
        $info['end_time'] = empty($_POST['end_time']) ? '': trim(htmlspecialchars($_POST['end_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['end_time'])) errorAlert('结束日期不能为空');
        $info['coupon'] = empty($_POST['coupon']) ? '': trim(htmlspecialchars($_POST['coupon'],ENT_QUOTES,'UTF-8'));
        if(empty($info['coupon'])) errorAlert('优惠内容不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('电话不能为空');
        $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
        if(empty($info['addr'])) errorAlert('活动地址不能为空');
        $info['details'] = empty($_POST['details']) ? '': getValue($_POST['details']);
        if(empty($info['details'])) errorAlert('活动详情不能为空');

        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) {
                $info['face_pic'] = $face_pic['web_file_name'];
                uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],509,293);
            } 
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!activityMdl::getInstance()->addActivity($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('添加了团购活动',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=activity&act=main'");
        die;
    } 
    $areas = areaMdl::getInstance()->getAreaPair();
    require TEMPLATE_PATH.'activity/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = activityMdl::getInstance()->getActivity($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('活动名称不能为空');

        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['face_pic'] = $data['face_pic'];
         $info['lng'] = empty($_POST['lng']) ? 0 : (int)($_POST['lng']*100000);//存整数到数据库
        $info['lat'] = empty($_POST['lat']) ? 0 : (int)($_POST['lat']*100000);
        $info['sj'] = empty($_POST['sj']) ? '': trim(htmlspecialchars($_POST['sj'],ENT_QUOTES,'UTF-8'));
        $info['reg_time'] = empty($_POST['reg_time']) ? '': trim(htmlspecialchars($_POST['reg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['reg_time'])) errorAlert('报名截止日期不能为空');
        $info['bg_time'] = empty($_POST['bg_time']) ? '': trim(htmlspecialchars($_POST['bg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['bg_time'])) errorAlert('开始日期不能为空');
        $info['end_time'] = empty($_POST['end_time']) ? '': trim(htmlspecialchars($_POST['end_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['end_time'])) errorAlert('结束日期不能为空');
        $info['coupon'] = empty($_POST['coupon']) ? '': trim(htmlspecialchars($_POST['coupon'],ENT_QUOTES,'UTF-8'));
        if(empty($info['coupon'])) errorAlert('优惠内容不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('电话不能为空');
        $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
        if(empty($info['addr'])) errorAlert('活动地址不能为空');
        $info['details'] = empty($_POST['details']) ? '': getValue($_POST['details']);
        if(empty($info['details'])) errorAlert('活动详情不能为空');
 
        $delpics = array();
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) {
                    $info['face_pic'] = $face_pic['web_file_name'];
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],509,293);
                    $delpics[] = $data['face_pic'];    
                }
            
        }  catch (Exception $e){
            if(empty($data['face_pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === activityMdl::getInstance()->updateActivity($id,$info)) errorAlert ('添加失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('修改了团购活动',$data,$info);
        echoJs("alert('添加成功');parent.location='index.php?ctl=activity&act=edit&id=".$id."'");
        die;
    } 
    $areas = areaMdl::getInstance()->getAreaPair();
    logsInt::getInstance()->systemLogs('打开了修改团购面板');
    require TEMPLATE_PATH.'activity/edit.html';
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
        $data = activityMdl::getInstance()->getActivity($id);    
        if(empty($data)) errorAlert ('参数出错');


        if(false !== activityMdl::getInstance()->delActivity($id)) {
            if(!empty($data['face_pic'])){
                if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
            }
            logsInt::getInstance()->systemLogs('删除了团购活动',$data,array()); 
        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=activity' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}


if($_GET['act'] === 'join'){
    import::getMdl('activityJoin');
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = activityMdl::getInstance()->getActivity($id);    
    if(empty($data)) errorAlert ('参数出错');
    $url = 'index.php?ctl=activity&act=join&id='.$id; 
    $where  = array('actvity_id'=>$id);
    $totalnum = activityJoinMdl::getInstance()->getActivityJoinCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`uid`','`name`','`tel`','`email`','`qq`','`num`','`addr`','`ip`');     
    $datas = activityJoinMdl::getInstance()->getActivityJoinList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了团购报名列表');
    require TEMPLATE_PATH.'activity/join.html'; 
    die;
}