<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('companyPics');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=companyPics&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = companyPicsMdl::getInstance()->getCompanyPicsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`type`','`uid`','`title`','`pic`');     
    $datas = companyPicsMdl::getInstance()->getCompanyPicsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了企业相册');
    require TEMPLATE_PATH.'companyPics/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('企业ID不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['pic'] = '';

        try{
            import::getLib('uploadimg');
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) $info['pic'] = $pic['web_file_name'];
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!companyPicsMdl::getInstance()->addCompanyPics($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了企业相册',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=companyPics&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'companyPics/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = companyPicsMdl::getInstance()->getCompanyPics($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        if(empty($info['uid'])) errorAlert('企业ID不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['pic'] = $data['pic'];
 
        $delpics = array();
        try{
            import::getLib('uploadimg');
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) {
                    $info['pic'] = $pic['web_file_name'];
                    $delpics[] = $data['pic'];    
                }
            
        }  catch (Exception $e){
            if(empty($data['pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === companyPicsMdl::getInstance()->updateCompanyPics($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('修改了企业相册',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=companyPics&act=edit&id=".$id."'");
        die;
    } 
    require TEMPLATE_PATH.'companyPics/edit.html';
    die;
        
}

if($_GET['act'] === 'view'){    
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = companyPicsMdl::getInstance()->getCompanyPics($id);    
    if(empty($data)) errorAlert ('参数出错');
    logsInt::getInstance()->systemLogs('查看了企业相册详情',$data,array());
    require TEMPLATE_PATH.'companyPics/view.html';
    die;
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = companyPicsMdl::getInstance()->getCompanyPics($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=companyPics' : $_GET['back_url'];
    if(false !== companyPicsMdl::getInstance()->delCompanyPics($id)) {
        logsInt::getInstance()->systemLogs('删除了企业相册',$data,array());
        if(!empty($data['pic'])){
            if(file_exists(BASE_PATH.$data['pic'])) unlink(BASE_PATH.$data['pic']);
        }
       
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

