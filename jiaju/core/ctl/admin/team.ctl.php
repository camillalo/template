<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('team');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=team&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = teamMdl::getInstance()->getTeamCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','`name`','`face_pic`','`tel`','`addr`','`orderby`','`is_security`');     
    $datas = teamMdl::getInstance()->getTeamList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);

    logsInt::getInstance()->systemLogs('查看了工队列表');
    require TEMPLATE_PATH.'team/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('工队队名不能为空');
        $info['face_pic'] = '';
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
     
        $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
        $info['info'] = empty($_POST['info']) ? '': trim(htmlspecialchars($_POST['info'],ENT_QUOTES,'UTF-8'));
        $info['orderby'] =empty($_POST['orderby']) ? 0: (int)$_POST['orderby'];
        $info['is_security'] =empty($_POST['is_security']) ? 0: (int)$_POST['is_security'];

        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['face_pic']['tmp_name'])){ 
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            
            if(!empty($face_pic['web_file_name'])){
                 uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],130,130);
                $info['face_pic'] = $face_pic['web_file_name'];
            }
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!teamMdl::getInstance()->addTeam($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增了工队',$info,array());    
        echoJs("alert('操作成功');parent.location='index.php?ctl=team&act=add'");
        die;
    } 
    require TEMPLATE_PATH.'team/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = teamMdl::getInstance()->getTeam($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('工队队名不能为空');
        $info['face_pic'] = $data['face_pic'];
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
      
        $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
        if(empty($info['addr'])) errorAlert('联系地址不能为空');
        $info['info'] = empty($_POST['info']) ? '': trim(htmlspecialchars($_POST['info'],ENT_QUOTES,'UTF-8'));
        $info['orderby'] =empty($_POST['orderby']) ? 0: (int)$_POST['orderby'];
        $info['is_security'] =empty($_POST['is_security']) ? 0: (int)$_POST['is_security'];
 
        $delpics = array();
        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['face_pic']['tmp_name'])){ 
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) {
                     uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],130,130);
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];    
                }
           } 
            
        }  catch (Exception $e){
            if(empty($data['face_pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === teamMdl::getInstance()->updateTeam($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
     
        logsInt::getInstance()->systemLogs('修改了工队',$data,$info);   
        echoJs("alert('操作成功');parent.location='index.php?ctl=team&act=edit&id=".$id."'");
        die;
    } 
     logsInt::getInstance()->systemLogs('打开了工队编辑模块');
    require TEMPLATE_PATH.'team/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = teamMdl::getInstance()->getTeam($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=team' : $_GET['back_url'];
    if(false !== teamMdl::getInstance()->delTeam($id)) {
        if(!empty($data['face_pic'])){
            if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
        }
 
        logsInt::getInstance()->systemLogs('删除了工队',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

