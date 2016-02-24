<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',100);//分页大小
import::getMdl('recommend');

if($_GET['act'] === 'main'){
    import::getMdl('recommendGroup');
  
    $pages = recommendGroupMdl::getInstance()->getAllRecommendGroup();
    $_GET['page_id'] = empty($_GET['page_id']) ? (isset($pages[0]['group_id']) ? (int)$pages[0]['group_id'] : 0) : (int)$_GET['page_id'];
    $url = 'index.php?ctl=recommend&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    
    if(!empty($_GET['page_id'])){
        $where['page_id'] = $_GET['page_id']; 
        $url.='&page_id='.  $_GET['page_id'];
        import::getMdl('recommendSign');
        $signs = recommendSignMdl::getInstance()->getRecommendSignByGroupId($_GET['page_id']);
    }else{
        $signs = array();
    }
    $_GET['sign_id'] = empty($_GET['sign_id']) ? (isset($signs[0]['id']) ? (int)$signs[0]['id'] : 0) : (int)$_GET['sign_id'];
    if(!empty($_GET['sign_id'])){
        $where['sign_id'] = $_GET['sign_id']; 
        $url.='&sign_id='.  $_GET['sign_id'];
    }

    $orderby = array('`order`'=>'ASC');
    $col = array('`recommend_id`','`title`','`page_id`','`sign_id`','`type`','`mdl_id`','`link`','`order`');
    $datas = recommendMdl::getInstance()->getRecommendList($col,$where,$orderby);
    logsInt::getInstance()->systemLogs('查看了推荐内容列表');
    require TEMPLATE_PATH.'recommend/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $recommend_id = empty ($_GET['recommend_id']) ? errorAlert('参数错误') : (int)$_GET['recommend_id'];    
    $data = recommendMdl::getInstance()->getRecommend($recommend_id);    
    if(empty($data)) errorAlert ('参数出错');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? errorAlert('标题不能为空') : trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        $info['type']  = empty($_POST['type']) ? 0 : (int)$_POST['type'];
        $info['mdl_id']  = empty($_POST['mdl_id']) ? 0 : (int)$_POST['mdl_id'];
        $info['page_id'] = empty($_POST['page_id']) ? errorAlert('请选择页面') : (int)$_POST['page_id'];
        $info['sign_id'] = empty($_POST['sign_id']) ? errorAlert('请选择推荐位') : (int)$_POST['sign_id'];
        $info['order']   = empty($_POST['order'])   ? 100 : (int)$_POST['order'];
        $info['face_pic']= empty($_POST['face_pic'])? ''  :  trim(htmlspecialchars($_POST['face_pic'],ENT_QUOTES,'UTF-8'));
        $info['link']    = empty($_POST['link'])? ''  :  trim(htmlspecialchars($_POST['link'],ENT_QUOTES,'UTF-8'));
        try{
            import::getLib('uploadimg');
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) $info['face_pic'] = $pic['web_file_name'];
        }  catch (Exception $e){}
        $info['description']= empty($_POST['description'])? ''  : stripslashes_deep($_POST['description']);
        if(false === recommendMdl::getInstance()->updateRecommend($recommend_id,$info)) errorAlert ('更新失败');
        logsInt::getInstance()->systemLogs('修改了推荐内容',$data,$info);
        errorAlert('操作成功');
        die;
    }
    import::getMdl('recommendGroup');
    import::getMdl('recommendSign');
    $pages = recommendGroupMdl::getInstance()->getAllRecommendGroup();
    $signs = recommendSignMdl::getInstance()->getRecommendSignByGroupId($data['page_id']);
    logsInt::getInstance()->systemLogs('打开了推荐内容修改页');
    require TEMPLATE_PATH.'recommend/edit.html';
    die;
}

if($_GET['act'] === 'add'){
    $_GET['type']   = empty($_GET['type'])   ? 0 : (int)$_GET['type'];
    $_GET['mdl_id'] = empty($_GET['mdl_id']) ? 0 : (int)$_GET['mdl_id'];
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? errorAlert('标题不能为空') : trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        $info['type']  = $_GET['type'];
        $info['mdl_id']  = $_GET['mdl_id'];

        $info['page_id'] = empty($_POST['page_id']) ? errorAlert('请选择页面') : (int)$_POST['page_id'];
        $info['sign_id'] = empty($_POST['sign_id']) ? errorAlert('请选择推荐位') : (int)$_POST['sign_id'];
        $info['order']   = empty($_POST['order'])   ? 100 : (int)$_POST['order'];
        $info['face_pic']= empty($_POST['face_pic'])? ''  :  trim(htmlspecialchars($_POST['face_pic'],ENT_QUOTES,'UTF-8'));
        $info['link']    = empty($_POST['link'])? ''  :  trim(htmlspecialchars($_POST['link'],ENT_QUOTES,'UTF-8'));
        try{
            import::getLib('uploadimg');
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) $info['face_pic'] = $pic['web_file_name'];
        }  catch (Exception $e){}
        $info['description']= empty($_POST['description'])? ''  : stripslashes_deep($_POST['description']);

        if(!recommendMdl::getInstance()->addRecommend($info)) errorAlert ('添加失败');
       
        logsInt::getInstance()->systemLogs('新增了推荐内容',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=recommend&act=main&page_id=". $info['page_id'] ."&sign_id=".$info['sign_id']."'");
        die;
    }
    import::getMdl('recommendGroup');
    $pages = recommendGroupMdl::getInstance()->getAllRecommendGroup();
    
    import::getInt('recommend');
    $data = recommend::getInstance()->getDataByTypeId($_GET['type'],$_GET['mdl_id']);
   
    require TEMPLATE_PATH.'recommend/add.html';
    die;
}


if($_GET['act'] === 'save'){
    $type = empty($_GET['type']) ? dieJsonErr('参数不正确') : (int)$_GET['type'];
    $ids = empty($_POST['id']) ? dieJsonErr('参数错误') : $_POST['id'];
    $signIds = empty($_POST['sign_id']) ? dieJsonErr('参数错误') : $_POST['sign_id'];
    
    import::getInt('recommend');
    
    $datas = array();
    foreach($ids as $id){
        $datas[] = recommend::getInstance()->getDataByTypeId($type,$id);
    }
    import::getMdl('recommendSign');
    $insertArr = array();
     foreach($signIds as $sid){
        $pageid = recommendSignMdl::getInstance()->getGroupIdbySignId($sid);
            foreach($datas as $val){
                $val['sign_id'] = $sid;
                $val['page_id'] = $pageid;
                $insertArr [] = $val;
            }
               
        }
    recommendMdl::getInstance()->addRecommendArr($insertArr);
    logsInt::getInstance()->systemLogs('新增了推荐位内容',$insertArr,array());
    dieJsonRight('操作成功');
    die;
}

if($_GET['act'] === 'del'){
    $recommend_id = empty ($_GET['recommend_id']) ? errorAlert('参数错误') : $_GET['recommend_id'];  
    $ids = array();
    if(is_array($recommend_id)){
        foreach($recommend_id as $v){
            $ids[] = (int)$v;
        }
    }else{
        $ids [] = (int)$recommend_id;
    }
     foreach($ids as $id){
        $data = recommendMdl::getInstance()->getRecommend($id);    
        if(false !== recommendMdl::getInstance()->delRecommend($id)) {
            logsInt::getInstance()->systemLogs('删除了推荐内容',$data,array()); 
        }
     }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=recommend' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}


if($_GET['act'] === 'update'){
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=recommend' : $_GET['back_url'];
    $order = empty($_POST['order']) ? array() : $_POST['order'];
    foreach($order as $k => $v){
        $info = array(
            'order' => (int)$v
        );
        $id = (int)$k;
        recommendMdl::getInstance()->updateRecommend($id,$info);
    }
    logsInt::getInstance()->systemLogs('更新了推荐内容排序',$order,array());
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}
