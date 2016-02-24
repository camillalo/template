<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('links');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=links&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = linksMdl::getInstance()->getLinksCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    $col = array('`id`','`link_name`','`link_url`','`link_pic`','`link_order`');     
    $datas = linksMdl::getInstance()->getLinksList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看友情链接列表');
    require TEMPLATE_PATH.'links/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['link_name'] = empty($_POST['link_name']) ? '': trim(htmlspecialchars($_POST['link_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['link_name'])) errorAlert('站点名称不能为空');
        $info['link_url'] = empty($_POST['link_url']) ? '': trim(htmlspecialchars($_POST['link_url'],ENT_QUOTES,'UTF-8'));
        if(empty($info['link_url'])) errorAlert('链接地址不能为空');
        $info['link_pic'] = '';
        $info['link_order'] =empty($_POST['link_order']) ? 0: (int)$_POST['link_order'];
        if(empty($info['link_order'])) errorAlert('排序不能为空');

        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['link_pic']['tmp_name'])){ 
            $link_pic = uploadImg::getInstance()->upload('link_pic');
            if(!empty($link_pic['web_file_name'])) $info['link_pic'] = $link_pic['web_file_name'];
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!linksMdl::getInstance()->addLinks($info)) errorAlert ('添加失败');
        logsInt::getInstance()->systemLogs('新增了友情链接',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=links&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'links/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = linksMdl::getInstance()->getLinks($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['link_name'] = empty($_POST['link_name']) ? '': trim(htmlspecialchars($_POST['link_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['link_name'])) errorAlert('站点名称不能为空');
        $info['link_url'] = empty($_POST['link_url']) ? '': trim(htmlspecialchars($_POST['link_url'],ENT_QUOTES,'UTF-8'));
        if(empty($info['link_url'])) errorAlert('链接地址不能为空');
        $info['link_pic'] = $data['link_pic'];
        $info['link_order'] =empty($_POST['link_order']) ? 0: (int)$_POST['link_order'];
        if(empty($info['link_order'])) errorAlert('排序不能为空');
 
        $delpics = array();
        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['link_pic']['tmp_name'])){ 
            $link_pic = uploadImg::getInstance()->upload('link_pic');
            if(!empty($link_pic['web_file_name'])) {
                    $info['link_pic'] = $link_pic['web_file_name'];
                    $delpics[] = $data['link_pic'];    
                }
           } 
            
        }  catch (Exception $e){
            if(empty($data['link_pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === linksMdl::getInstance()->updateLinks($id,$info)) errorAlert ('添加失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('编辑了友情链接',$data,$info);
        echoJs("alert('添加成功');parent.location='index.php?ctl=links&act=edit&id=".$id."'");
        die;
    } 
    logsInt::getInstance()->systemLogs('打开了友情链接编辑面板');
    require TEMPLATE_PATH.'links/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = linksMdl::getInstance()->getLinks($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=links' : $_GET['back_url'];
    if(false !== linksMdl::getInstance()->delLinks($id)) {
        logsInt::getInstance()->systemLogs('删除了友情链接',$data,array());
        if(!empty($data['link_pic'])){
            if(file_exists(BASE_PATH.$data['link_pic'])) unlink(BASE_PATH.$data['link_pic']);
        }
       
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

