<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('bidding');
import::getMdl('area');
import::getInt('category');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=bidding&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
   
    if(isset($_GET['is_show']) && $_GET['is_show'] != 999){  
        $_GET['is_show'] = (int)$_GET['is_show'];  
        $url.='&is_show='. $_GET['is_show'];
        $where['is_show'] = $_GET['is_show'];
    }else{
        $_GET['is_show'] = 999;
    }

    $_GET['is_key'] = empty($_GET['is_key']) ? 0 : (int)$_GET['is_key'];
    if(!empty($_GET['is_key'])){
        $url.='&is_key='. $_GET['is_key'];
        $where['is_key'] = $_GET['is_key'];
    }
    $_GET['is_supervision'] = empty($_GET['is_supervision']) ? 0 : (int)$_GET['is_supervision'];
    if(!empty($_GET['is_supervision'])){
        $url.='&is_supervision='. $_GET['is_supervision'];
        $where['is_supervision'] = $_GET['is_supervision'];
    }
    $_GET['is_material'] = empty($_GET['is_material']) ? 0 : (int)$_GET['is_material'];
    if(!empty($_GET['is_material'])){
        $url.='&is_material='. $_GET['is_material'];
        $where['is_material'] = $_GET['is_material'];
    }
    $totalnum = biddingMdl::getInstance()->getBiddingCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`name`','`area_id`','`mobile`','`sex`','`building_name`','`addr`','`way`','`area`','`gold`','`is_show`','`create_time`');     
    $datas = biddingMdl::getInstance()->getBiddingList($col,$where,$orderby,$begin,PAGE_SIZE);
    foreach($datas as $k=>$val){
        $area = areaMdl::getInstance()->getArea($val['area_id']);
        $datas[$k]['area_name'] = isset($area['area_name']) ? $area['area_name'] : '' ;
    }
    logsInt::getInstance()->systemLogs('查看了招标列表');
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'bidding/main.html';
    die;
}



if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingMdl::getInstance()->getBidding($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    $areas = areaMdl::getInstance()->getAreaPair();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('发布人不能为空');
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('区域不能为空');
        $info['mobile'] = empty($_POST['mobile']) ? '': trim(htmlspecialchars($_POST['mobile'],ENT_QUOTES,'UTF-8'));
        if(empty($info['mobile'])) errorAlert('手机不能为空');
        $info['sex'] =empty($_POST['sex']) ? 0: (int)$_POST['sex'];
        if(empty($info['sex'])) errorAlert('性别不能为空');
        $info['building_name'] = empty($_POST['building_name']) ? '': trim(htmlspecialchars($_POST['building_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['building_name'])) errorAlert('楼盘名称不能为空');
        $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
        if(empty($info['addr'])) errorAlert('详细地址不能为空');
        $info['way'] =empty($_POST['way']) ? 0: (int)$_POST['way'];
        $info['type_root'] = empty($_POST['type_root']) ?  0 : (int)$_POST['type_root'];
        if(empty($_POST['type_root'])) errorAlert('空间类型不能为空');
        $info['type_id'] =empty($_POST['type_id']) ? 0: (int)$_POST['type_id'];
        $info['style_id'] =empty($_POST['style_id']) ? 0: (int)$_POST['style_id'];
        if(empty($info['style_id'])) errorAlert('最喜欢风格不能为空');
        $info['budget_id'] =empty($_POST['budget_id']) ? 0: (int)$_POST['budget_id'];
        if(empty($info['budget_id'])) errorAlert('预算不能为空');
         $info['area'] =empty($_POST['area']) ? 0: htmlspecialchars($_POST['area'],ENT_QUOTES,'UTF-8');
        if(empty($info['area'])) errorAlert('面积㎡不能为空');
        $info['start_time'] =empty($_POST['start_time']) ? '' : htmlspecialchars($_POST['start_time'],ENT_QUOTES,'UTF-8' );
        if(empty($info['start_time'])) errorAlert('开始装修时间不能为空');
        $info['is_key'] =empty($_POST['is_key']) ? 0: (int)$_POST['is_key'];
        if(empty($info['is_key'])) errorAlert('是否拿到钥匙不能为空');
        $info['is_supervision'] =empty($_POST['is_supervision']) ? 0: (int)$_POST['is_supervision'];
        if(empty($info['is_supervision'])) errorAlert('是否有监理需求不能为空');
        $info['is_material'] =empty($_POST['is_material']) ? 0: (int)$_POST['is_material'];
        if(empty($info['is_material'])) errorAlert('是否有材料需求不能为空');
        $info['demand'] = empty($_POST['demand']) ? '': trim(htmlspecialchars($_POST['demand'],ENT_QUOTES,'UTF-8'));
        if(empty($info['demand'])) errorAlert('要求不能为空');
        
        $info['gold'] =empty($_POST['gold']) ? 0: (int)$_POST['gold'];
        
         $info['feedback'] = empty($_POST['feedback']) ? '': trim(htmlspecialchars($_POST['feedback'],ENT_QUOTES,'UTF-8'));
        if(empty($info['feedback'])) errorAlert('客服反馈不能为空');
        $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(empty($info['is_show'])) errorAlert('是否显示不能为空');
        
        if(false === biddingMdl::getInstance()->updateBidding($id,$info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('编辑了招标信息',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=bidding&act=edit&id=".$id."'");
        die;
    } 
    
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    $child = empty($data['type_root']) ?  array() : category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$data['type_root']);    
    $styles = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['style']); 
    $budgets = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['budget']);
    require TEMPLATE_PATH.'bidding/edit.html';
    die;   
}

if($_GET['act'] === 'view'){    
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingMdl::getInstance()->getBidding($id);    
    if(empty($data)) errorAlert ('参数出错');
    logsInt::getInstance()->systemLogs('查看了招标详情',$data,array());
    $area = areaMdl::getInstance()->getArea($data['area_id']);
    
    import::getMdl('biddingLook');
    $looks = biddingLookMdl::getInstance()->getBiddingLookSbyId($id);

    require TEMPLATE_PATH.'bidding/view.html';
    die;
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingMdl::getInstance()->getBidding($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=bidding' : $_GET['back_url'];
    if(false !== biddingMdl::getInstance()->delBidding($id)) {
        logsInt::getInstance()->systemLogs('删除了招标信息',$data,array());
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'setting'){
    $data = import::getCfg('biddingSetting');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = empty($_POST['biddingSetting']) ?  5 : (int)$_POST['biddingSetting'] ;
        makeCfg('biddingSetting', $data);
    }
    logsInt::getInstance()->systemLogs('招标设置管理',$data,array());
    require TEMPLATE_PATH.'bidding/setting.html';
    die;
}