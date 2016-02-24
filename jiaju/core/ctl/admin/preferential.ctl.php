<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('preferential');
import::getMdl('preferentialKeywordMaps');
import::getLib('pscws5');
import::getMdl('keywords');
import::getInt('area');

if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=preferential&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = preferentialMdl::getInstance()->getPreferentialCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','is_show','`title`','`face_pic`');     
    $datas = preferentialMdl::getInstance()->getPreferentialList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了优惠信息列表');
    require TEMPLATE_PATH.'preferential/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('优惠标题不能为空');
     
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['face_pic'] = '';
        $info['content'] = empty($_POST['content']) ? '': getValue($_POST['content']);
        $info['create_time'] = NOWTIME;
        $info['ip'] = getIp();
        $info['is_show'] = empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(empty($info['content'])) errorAlert('优惠内容不能为空');

        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['face_pic']['tmp_name'])){ 
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $id = preferentialMdl::getInstance()->addPreferential($info);
        if(!$id) errorAlert ('操作失败');
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['preferential_id'] = $id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            preferentialKeywordMapsMdl::getInstance()->addPreferentialKeywordMaps($mapinfo);
        }
        logsInt::getInstance()->systemLogs('修改了优惠信息',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=preferential&act=add'");
        die;
    } 
   $areas = areaInt::getInstance()->getAreas();
    logsInt::getInstance()->systemLogs('打开了编辑优惠信息页面');
    require TEMPLATE_PATH.'preferential/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = preferentialMdl::getInstance()->getPreferential($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('优惠标题不能为空');
       
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['face_pic'] = $data['face_pic'];
        $info['content'] = empty($_POST['content']) ? '': getValue($_POST['content']);
        if(empty($info['content'])) errorAlert('优惠内容不能为空');
        $info['is_show'] = empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
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
        
        if(false === preferentialMdl::getInstance()->updatePreferential($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        
        preferentialKeywordMapsMdl::getInstance()->delPreferentialKeywordMapsByPreferentialId($id);
        
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['preferential_id'] = $id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            preferentialKeywordMapsMdl::getInstance()->addPreferentialKeywordMaps($mapinfo);
        }
        logsInt::getInstance()->systemLogs('新增了优惠信息',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=preferential&act=edit&id=".$id."'");
        die;
    } 

    $areas = areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'preferential/edit.html';
    die;
        
}

if($_GET['act'] === 'view'){    
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = preferentialMdl::getInstance()->getPreferential($id);    
    if(empty($data)) errorAlert ('参数出错');
    logsInt::getInstance()->systemLogs('查看了优惠信息详情');
    require TEMPLATE_PATH.'preferential/view.html';
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
            $data = preferentialMdl::getInstance()->getPreferential($id);    
            if(empty($data)) errorAlert ('参数出错');

            if(false !== preferentialMdl::getInstance()->delPreferential($id)) {
                logsInt::getInstance()->systemLogs('删除了优惠信息',$data,array());
                if(!empty($data['face_pic'])){
                    if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
                }
                preferentialKeywordMapsMdl::getInstance()->delPreferentialKeywordMapsByPreferentialId($id);

            }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=preferential' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}