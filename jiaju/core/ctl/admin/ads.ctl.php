<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('ads');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=ads&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }

    $_GET['site_id'] = empty($_GET['site_id']) ? 0 : (int)$_GET['site_id'];
    if(!empty($_GET['site_id'])){
        $url.='&site_id='. $_GET['site_id'];
        $where['site_id'] = $_GET['site_id'];
    }
    
    $totalnum = adsMdl::getInstance()->getAdsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=>'ASC');   
    $col = array('`id`','`site_id`','`title`','`pic`','`code`','`bg_time`','`end_time`','orderby');     
    $datas = adsMdl::getInstance()->getAdsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    import::getMdl('adSite');
    $adSites = adSiteMdl::getInstance()->getAdSitePair();
    logsInt::getInstance()->systemLogs('查看了广告列表');
    require TEMPLATE_PATH.'ads/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['site_id'] =empty($_POST['site_id']) ? 0: (int)$_POST['site_id'];
        if(empty($info['site_id'])) errorAlert('广告位不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('广告名称不能为空');
        $info['pic'] = '';
        $info['code'] = empty($_POST['code']) ? '': trim($_POST['code']);
        $info['bg_time'] =empty($_POST['bg_time']) ? 0: (int)(strtotime($_POST['bg_time']));
        if(empty($info['bg_time'])) errorAlert('生效时间不能为空');
        $info['end_time'] =empty($_POST['end_time']) ? 0: (int)(strtotime($_POST['end_time']));
        if(empty($info['end_time'])) errorAlert('失效时间不能为空');
        $info['orderby'] =empty($_POST['orderby']) ? 999: (int)$_POST['orderby'];
        $info['link'] = empty($_POST['link']) ? '': trim($_POST['link']);
        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['pic']['tmp_name'])){ 
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) $info['pic'] = $pic['web_file_name'];
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!adsMdl::getInstance()->addAds($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了广告',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=ads&act=add'");
        die;
    } 
    import::getMdl('adSite');
    $adSites = adSiteMdl::getInstance()->getAdSitePair();
  
    require TEMPLATE_PATH.'ads/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = adsMdl::getInstance()->getAds($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['site_id'] =empty($_POST['site_id']) ? 0: (int)$_POST['site_id'];
        if(empty($info['site_id'])) errorAlert('广告位不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('广告名称不能为空');
        $info['pic'] = $data['pic'];
        $info['code'] = empty($_POST['code']) ? '': trim($_POST['code']);
        $info['bg_time'] =empty($_POST['bg_time']) ? 0: (int)(strtotime($_POST['bg_time']));
        if(empty($info['bg_time'])) errorAlert('生效时间不能为空');
        $info['end_time'] =empty($_POST['end_time']) ? 0: (int)(strtotime($_POST['end_time']));
        if(empty($info['end_time'])) errorAlert('失效时间不能为空');
        $info['orderby'] =empty($_POST['orderby']) ? 999: (int)$_POST['orderby'];
        $info['link'] = empty($_POST['link']) ? '': trim($_POST['link']);
        $delpics = array();
        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['pic']['tmp_name'])){ 
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) {
                    $info['pic'] = $pic['web_file_name'];
                    $delpics[] = $data['pic'];    
                }
           } 
            
        }  catch (Exception $e){
            if(empty($data['pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === adsMdl::getInstance()->updateAds($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('修改了广告',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=ads&act=edit&id=".$id."'");
        die;
    } 
    import::getMdl('adSite');
    $adSites = adSiteMdl::getInstance()->getAdSitePair();
    logsInt::getInstance()->systemLogs('打开了广告编辑面板');
    require TEMPLATE_PATH.'ads/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = adsMdl::getInstance()->getAds($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=ads' : $_GET['back_url'];
    if(false !== adsMdl::getInstance()->delAds($id)) {
        if(!empty($data['pic'])){
            if(file_exists(BASE_PATH.$data['pic'])) unlink(BASE_PATH.$data['pic']);
        }
       logsInt::getInstance()->systemLogs('删除了广告',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

