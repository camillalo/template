<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('company');
import::getMdl('companyArea');
import::getMdl('companyProject');
import::getMdl('companyKeywordMaps');
import::getMdl('area');
import::getInt('category');
import::getLib('pscws5');
import::getMdl('keywords');
import::getMdl('users');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=company&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $_GET['type'] = empty($_GET['type']) ? 0  : (int)$_GET['type'];
    if(!empty($_GET['type'])){
        $url.='&type='.$_GET['type'];
        $where['type'] = $_GET['type'];
    }

    $totalnum = companyMdl::getInstance()->getCompanyCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=>'DESC','uid'=>'DESC');   
    $col = array('`uid`','type','`area_id`','`company_name`','`founding_year`','`orderby`');     
    $datas = companyMdl::getInstance()->getCompanyList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    foreach($datas as $k=>$val){
        $area = areaMdl::getInstance()->getArea($val['area_id']);
        $datas[$k]['area_name'] = isset($area['area_name']) ? $area['area_name'] : '' ;
        $user = usersMdl::getInstance()->getUsers($val['uid']);
        $datas[$k]['username'] = empty($user['username']) ? '该用户已经删除' : $user['username'];
        $datas[$k]['is_authentication'] = usersMdl::getInstance()->checkIsAuthentication($val['uid']);
    }

    logsInt::getInstance()->systemLogs('查看了公司列表');
    require TEMPLATE_PATH.'company/main.html';
    die;
}

if($_GET['act'] === 'del'){
    $uid = empty ($_GET['uid']) ? (empty($_GET['id']) ? errorAlert('参数错误') : $_GET['id']) : (int)$_GET['uid'];    
    
    $ids = array();
    if(is_array($uid)){
        foreach($uid as $v){
            $ids[] = (int)$v;
        }
    }else{
        $ids [] = (int)$uid;
    }  
    
     foreach($ids as $id){
            $data = companyMdl::getInstance()->getCompany($id);    
            if(empty($data)) errorAlert ('参数出错');
         
            if(false !== companyMdl::getInstance()->delCompany($id)) {
                logsInt::getInstance()->systemLogs('删除了公司信息',$data,array());
                if(!empty($data['logo'])){
                    if(file_exists(BASE_PATH.$data['logo'])) unlink(BASE_PATH.$data['logo']);
                }
                if(!empty($data['banner'])){
                        if(file_exists(BASE_PATH.$data['banner'])) unlink(BASE_PATH.$data['banner']);
                }
                companyKeywordMapsMdl::getInstance()->delCompanyKeywordMapsByuid($id);

            }
     }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=company' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    die;
}

if($_GET['act'] === 'authentication'){
    $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
    $data = companyMdl::getInstance()->getCompany($uid);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=company' : $_GET['back_url'];
    $info['is_authentication']  = 1;
    $info['uid'] = $uid;
    import::getMdl('users');
    if(false !== usersMdl::getInstance()->replaceUsersEx($info)){
        logsInt::getInstance()->systemLogs('修改了公司的认证信息',$data,$info);
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'orderby'){
   $uid = empty ($_GET['uid']) ? errorAlert('参数错误') : (int)$_GET['uid'];    
   $data = companyMdl::getInstance()->getCompany($uid);    
   if(empty($data)) errorAlert ('参数出错');
   if($_SERVER['REQUEST_METHOD'] === 'POST'){
       $info['orderby'] = empty($_POST['orderby']) ? 0 : (int)$_POST['orderby'];
       $info['longitude'] = empty($_POST['longitude']) ? 0 : (int)($_POST['longitude']*100000);//存整数到数据库
       $info['latitude'] = empty($_POST['latitude']) ? 0 : (int)($_POST['latitude']*100000);
       
        if(false !== companyMdl::getInstance()->updateCompany($uid,$info)){
             logsInt::getInstance()->systemLogs('修改了公司的推广信息',$data,$info);   
             errorAlert('操作成功');
        }
        errorAlert('操作失败');
       die;
   }  
   require  TEMPLATE_PATH.'company/orderby.html';
   die;
}