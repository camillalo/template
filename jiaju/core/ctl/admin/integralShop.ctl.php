<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('integralShop');
if($_GET['act'] === 'main'){
    $url = 'index.php?ctl=integralShop&act=main'; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array();
    if(!empty($_GET['keyword'])){
        $url.='&keyword='.  urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $totalnum = integralShopMdl::getInstance()->getIntegralShopCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    
    $col = array('`id`','`product_name`','`face_pic`','`num`','`market_price`','`exchange_integral`','`lottery_integral`','`lottery_probability`','is_show');     
    $datas = integralShopMdl::getInstance()->getIntegralShopList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了积分商城列表');
    require TEMPLATE_PATH.'integralShop/main.html';
    die;
}

if($_GET['act'] === 'add'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['product_name'] = empty($_POST['product_name']) ? '': trim(htmlspecialchars($_POST['product_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['product_name'])) errorAlert('产品名称不能为空');
        $info['face_pic'] = '';
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        if(empty($info['num'])) errorAlert('库存不能为空');
        $info['market_price'] =empty($_POST['market_price']) ? 0: (int)($_POST['market_price']*100);
        if(empty($info['market_price'])) errorAlert('市场价格不能为空');
        $info['exchange_integral'] =empty($_POST['exchange_integral']) ? 0: (int)$_POST['exchange_integral'];
        if(empty($info['exchange_integral'])) errorAlert('兑换消耗积分不能为空');
        $info['lottery_integral'] =empty($_POST['lottery_integral']) ? 0: (int)$_POST['lottery_integral'];
        if(empty($info['lottery_integral'])) errorAlert('抽奖消耗积分不能为空');
        $info['lottery_probability'] =empty($_POST['lottery_probability']) ? 0: (int)$_POST['lottery_probability'];
        if(empty($info['lottery_probability'])) errorAlert('抽奖概率不能为空');

        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!integralShopMdl::getInstance()->addIntegralShop($info)) errorAlert ('操作失败');
        logsInt::getInstance()->systemLogs('新增加了积分商城商品',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=integralShop&act=add'");
        die;
    } 

    require TEMPLATE_PATH.'integralShop/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = integralShopMdl::getInstance()->getIntegralShop($id);    
    if(empty($data)) errorAlert ('参数出错');
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['product_name'] = empty($_POST['product_name']) ? '': trim(htmlspecialchars($_POST['product_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['product_name'])) errorAlert('产品名称不能为空');
        $info['face_pic'] = $data['face_pic'];
        $info['num'] =empty($_POST['num']) ? 0: (int)$_POST['num'];
        if(empty($info['num'])) errorAlert('库存不能为空');
        $info['market_price'] =empty($_POST['market_price']) ? 0: (int)($_POST['market_price']*100);
        if(empty($info['market_price'])) errorAlert('市场价格不能为空');
        $info['exchange_integral'] =empty($_POST['exchange_integral']) ? 0: (int)$_POST['exchange_integral'];
        if(empty($info['exchange_integral'])) errorAlert('兑换消耗积分不能为空');
        $info['lottery_integral'] =empty($_POST['lottery_integral']) ? 0: (int)$_POST['lottery_integral'];
        if(empty($info['lottery_integral'])) errorAlert('抽奖消耗积分不能为空');
        $info['lottery_probability'] =empty($_POST['lottery_probability']) ? 0: (int)$_POST['lottery_probability'];
        if(empty($info['lottery_probability'])) errorAlert('抽奖概率不能为空');
 
        $delpics = array();
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) {
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];    
                }
            
        }  catch (Exception $e){
            if(empty($data['face_pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === integralShopMdl::getInstance()->updateIntegralShop($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('修改了积分商城商品',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=integralShop&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'integralShop/edit.html';
    die;
        
}


if($_GET['act'] === 'show'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = integralShopMdl::getInstance()->getIntegralShop($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=integralShop' : $_GET['back_url'];
    $info['is_show'] = $data['is_show'] ? 0 : 1;
    if(false !== integralShopMdl::getInstance()->updateIntegralShop($id,$info)) {
       
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

