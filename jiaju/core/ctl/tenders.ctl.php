<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE', 10);
import::getInt('category');
if($_GET['act'] === 'main'){
    import::getMdl('bidding');
    $where = array(
        'is_show' => 1
    );
    $__SETTING['title'] .= '招标';
    $url = array();
    $_GET['t'] = empty($_GET['t']) ? 0 : (int)$_GET['t'];
    if(!empty($_GET['t'])){
        $url['t'] =  $_GET['t'];
        $where['type_root'] = $_GET['t'];
        $__SETTING['title'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['bidding'],$_GET['t']).'招标';
    }
    
    $_GET['area_id'] = empty($_GET['area_id']) ? 0 : (int)$_GET['area_id'];
    if(!empty($_GET['area_id'])){
        $url['area_id'] =  $_GET['area_id'];
        $where['area_id'] = $_GET['area_id'];
    }
    $_GET['is_supervision'] = empty($_GET['is_supervision']) ? 0 : (int)$_GET['is_supervision'];
    if(!empty($_GET['is_supervision'])){
        $url['is_supervision'] =  $_GET['is_supervision'];
        $where['is_supervision'] = $_GET['is_supervision'];
    }
    $_GET['is_material'] = empty($_GET['is_material']) ? 0 : (int)$_GET['is_material'];
    if(!empty($_GET['is_material'])){
        $url['is_material'] = $_GET['is_material'];
        $where['is_material'] = $_GET['is_material'];
    } 
    $totalnum = biddingMdl::getInstance()->getBiddingCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`area_id`','`type_root`','`budget_id`','`building_name`','`way`','`create_time`','`area`','`is_supervision`','`is_material`');     
    $datas = biddingMdl::getInstance()->getBiddingList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('tenders','main',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    

    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('tenders');
    require TEMPLATE_PATH.'tenders.html';
    die;
}

if($_GET['act'] === 'detail'){
    $id = empty($_GET['id']) ? show404() : (int)$_GET['id'];
    import::getMdl('bidding');
    $data = biddingMdl::getInstance()->getBidding($id);
    if(empty($data)) show404 ();
    if(!$data['is_show']) show404 ();
    $uid = (int)getUid();
    if(!empty($uid)){
        import::getMdl('biddingLook');
        $lookinfo = biddingLookMdl::getInstance()->getBiddingLook($uid,$id);
        $look =  isset($lookinfo['type']) ? (int)$lookinfo['type'] : 0;
    }
    $_GET['t'] = empty($_GET['t']) ? 0 : (int)$_GET['t'];
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    $areas = areaInt::getInstance()->getAreas();
    $newBidding = biddingMdl::getInstance()->getNewBiddings(5);
    $__SETTING['title'] = isset($areas[$data['area_id']])  ? $areas[$data['area_id']].$data['building_name'] . category::getInstance()->getCategoryName($__CATEGORY_TYPE['bidding'],$data['type_root']) : $data['building_name'] . category::getInstance()->getCategoryName($__CATEGORY_TYPE['bidding'],$data['type_root']);
    $pv = $data['pv'] + 1;
    biddingMdl::getInstance()->updateBidding($id,array('pv'=>$pv));
    
    
    //竞标模块
    import::getMdl('biddingBid');
    $where  = array(
        'bid' => $id,
        'is_show' => 1
    );
    $url = array('id'=>$id);
    $orderby = array('a.is_win'=>'desc','a.is_shortlisted'=>'desc','a.`t`'=>'desc','a.id'=>'DESC');  
      
    $_GET['ty'] = empty($_GET['ty']) ? 0 : (int)$_GET['ty'];
    switch ($_GET['ty']){
        case 1:
            $orderby = array('total'=> 'asc');
            break;
        case 2:
            $where['is_shortlisted'] = 1;
            break;
        case 3:
            $where['is_win'] = 1;
            break;
    }
    $url['ty'] = $_GET['ty'];
    $totalnum = biddingBidMdl::getInstance()->getBiddingBidCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
  
    $col = array('a.*','b.`type`');     
    $datas = biddingBidMdl::getInstance()->getBiddingBidList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('tenders','detail',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    $comIds = array();
    $deIds = array();
    $countIds = array();
    import::getMdl('users');
    foreach($datas as $k=>$val){
        switch ($val['type']){
            case $__USER_TYPE['company']:
            case $__USER_TYPE['material']:
                $comIds[] = $val['uid'];
                break;
            case $__USER_TYPE['designer']:
                $deIds[] = $val['uid'];
                break;
        }
        $countIds[] = $val['uid'];
        $datas[$k]['is_authentication'] = usersMdl::getInstance()->checkIsAuthentication($val['uid']);
    }
    import::getMdl('company');
    $companyInfo = companyMdl::getInstance()->getCompanysByIds($comIds);
    import::getMdl('designer');
    $designerInfo = designerMdl::getInstance()->getDesignerInfoByUids($deIds);
    $countinfo = biddingBidMdl::getInstance()->getCounts($countIds);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('tendersshow',array('buildingname'=>$data['building_name']));
    require TEMPLATE_PATH.'tenders_detail.html';
    die;
}

//使用VIP来查看招标的联系方式
if($_GET['act'] === 'use'){
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];
    import::getMdl('bidding');
    $data = biddingMdl::getInstance()->getBidding($id);
    if(empty($data)) errorAlert('参数错误');
    if(!$data['is_show'])errorAlert('参数错误');
    $maxNum = import::getCfg('biddingSetting');
    $maxNum = empty($maxNum) ? 5 : (int)$maxNum;
    
    $uid = (int)getUid();
    if(empty($uid)) errorAlert ('非法操作');
    if((int)$data['uid'] === $uid) errorAlert ('非法操作');
    import::getMdl('users');
    $userinfo = usersMdl::getInstance()->getUsers($uid);
    if(empty($userinfo)) errorAlert ('请登陆后再试');  
    if((int)$userinfo['type'] === $__USER_TYPE['owner']) errorAlert ('不允许操作！您是业主！');
    import::getMdl('biddingLook');
    $lookinfo = biddingLookMdl::getInstance()->getBiddingLook($uid,$id);
    $look = isset($lookinfo['type']) ? (int)$lookinfo['type'] : 0;
    if($look) errorAlert ('已经开通过了请刷新页面再试！');
    $num = (int) biddingLookMdl::getInstance()->getBiddingLookCount(array('bidding_id' => $id));
    if($num >= $maxNum) errorAlert ('很抱歉您来晚了！已经超过'.$maxNum.'商家使用VIP查看了！');
    if($userinfo['day'] < NOWTIME){
        if($userinfo['num'] > 0){
            $update['num'] = $userinfo['num'] - 1;
            if(false === usersMdl::getInstance()->updateUsers($uid,$update) ) errorAlert ('系统繁忙');
        }elseif($userinfo['gold'] > 0){
            $gold = empty($data['gold']) ?  0 : (int)$data['gold'];
            if(empty($gold)) errorAlert ('该条信息暂时不支持金币查看');
            if($userinfo['gold'] < $gold) errorAlert ('账户金币余额不足！');
            $update['gold'] =  $userinfo['gold'] - $gold;
            if(false === usersMdl::getInstance()->updateUsers($uid,$update) ) errorAlert ('系统繁忙');
        }        
        else {
            errorAlert ('您没有VIP特权或已经过期！');
        } 
    }   
    $info['uid'] = $uid;
    $info['bidding_id'] = $id;
    $info['type'] = 1;
    if(biddingLookMdl::getInstance()->replaceBiddingLook($info)) dieJs ('alert("操作成功");parent.location="'.mkUrl::linkTo('tenders','detail',array('id'=>$id)).'"');
    errorAlert('操作失败！');
    die;
}


if($_GET['act'] === 'save'){
    $info['uid']     = (int)getUid();
    $info['area_id'] = empty($_POST['area_id']) ? errorAlert('方便留下您大概的区域么') : (int)$_POST['area_id'];
    $info['budget_id']     = empty($_POST['budget_id'])  ? errorAlert(',请问您大概的装修预算是多少呢？') : (int)$_POST['budget_id'];
    $info['name']    = empty($_POST['name']) ? errorAlert('，您的称呼忘记填写了！') : htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8');
    $info['mobile']  = empty($_POST['mobile']) ? errorAlert('，您的联系方式忘记填写了！') : $_POST['mobile'];
    if(!isMobile($info['mobile'])) errorAlert ('手机号码错啦！');
    $info['area'] =empty($_POST['area']) ?  0 : htmlspecialchars($_POST['area'],ENT_QUOTES,'UTF-8');
    $info['building_name'] = empty($_POST['building_name']) ? '': trim(htmlspecialchars($_POST['building_name'],ENT_QUOTES,'UTF-8'));
    $info['type_root'] = empty($_POST['type_root']) ?  0 : (int)$_POST['type_root'];
    $info['type_id'] =empty($_POST['type_id']) ? 0: (int)$_POST['type_id'];
    $info['create_time'] = NOWTIME;
    $info['create_ip']   = getIp();
    import::getMdl('bidding');
    $id = biddingMdl::getInstance()->addBidding($info);
    if($id){
        setCk('tenders',$id);
        import::getInt('sms');
        smsInt::getInstance()->send('tenders',array($info['mobile']),array('name'=>$info['name'],'site_name'=>$__SETTING['site_name']));
        smsInt::getInstance()->sendToAdmin('tendersadmin',array('name'=>$info['name'],'mobile'=>$info['mobile']));
        dieJs('alert("发布成功！，您可以继续补充资料以便更好的为您提供优质服务！");parent.location="'.URL.'index.php?ctl=tenders&act=added"');       
    }
    errorAlert('发布失败');
    die;
}

if($_GET['act'] === 'save2'){
    $info['uid']     = (int)getUid();
    $info['mobile']  = empty($_POST['mobile']) ? errorAlert('，您的联系方式忘记填写了！') : $_POST['mobile'];
    if(!isMobile($info['mobile'])) errorAlert ('手机号码错啦！');
    $info['create_time'] = NOWTIME;
    $info['create_ip']   = getIp();
    import::getMdl('biddingQuick');
    if(biddingQuickMdl::getInstance()->addBiddingQuick($info)){
        import::getInt('sms');
        smsInt::getInstance()->send('fast',array($info['mobile']),array('site_name'=>$__SETTING['site_name']));
        smsInt::getInstance()->sendToAdmin('fastadmin',array('mobile'=>$info['mobile']));
        dieJs ('alert("恭喜您发布成功!");parent.location="'.mkUrl::linkTo('tenders').'"');
    }
    errorAlert('sorry！系统繁忙');
    die;
}

if($_GET['act'] === 'added'){
    import::getMdl('bidding');
    import::getInt('category');
    $id = (int)getCk('tenders');
    if(empty($id)){
        header("Location: ".mkUrl::linkTo('index'));
        die;
    }
    $data = biddingMdl::getInstance()->getBidding($id);
    if($data['create_ip'] != getIp()){
        header("Location: ".mkUrl::linkTo('index'));
        die;
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
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
        $info['start_time'] =empty($_POST['start_time']) ? 0: htmlspecialchars($_POST['start_time'],ENT_QUOTES,'UTF-8');
        if(empty($info['start_time'])) errorAlert('开始装修时间不能为空');
        $info['is_key'] =empty($_POST['is_key']) ? 0: (int)$_POST['is_key'];
        if(empty($info['is_key'])) errorAlert('是否拿到钥匙不能为空');
        $info['is_supervision'] =empty($_POST['is_supervision']) ? 0: (int)$_POST['is_supervision'];
        if(empty($info['is_supervision'])) errorAlert('是否有监理需求不能为空');
        $info['is_material'] =empty($_POST['is_material']) ? 0: (int)$_POST['is_material'];
        if(empty($info['is_material'])) errorAlert('是否有材料需求不能为空');
        $info['demand'] = empty($_POST['demand']) ? '': trim(htmlspecialchars($_POST['demand'],ENT_QUOTES,'UTF-8'));
        if(empty($info['demand'])) errorAlert('要求不能为空');
        $info['is_show'] = 0;
        $info['sex'] =empty($_POST['sex']) ? 0: (int)$_POST['sex'];
        if(false === biddingMdl::getInstance()->updateBidding($id,$info)) errorAlert ('操作失败');
        setCk('tenders',null);
        $companyIds = empty($_POST['companyId']) ? array() : $_POST['companyId'];
        import::getMdl('biddingLook');
        import::getMdl('users');
        $data = array('bidding_id'=>$id,'type'=>1);
        //print_r($companyIds);
        //die;
        $mobile = array();
        foreach($companyIds as $val){
            $val = (int)$val;
            $userinfo = usersMdl::getInstance()->getUsers($val);
            if($userinfo['day'] > NOWTIME){
                $data['uid'] = $val;
                $mobile[] = $userinfo['mobile'];
                biddingLookMdl::getInstance()->replaceBiddingLook($data);
            }
        }
        import::getInt('sms');
        smsInt::getInstance()->send('choose',$mobile,array('name'=>$data['name'],'mobile'=>$data['mobile'],'site_name'=>$__SETTING['site_name']));
        echoJs("alert('发布成功');parent.location='".mkUrl::linkTo('index')."'");
        die;
    }    
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    $child = empty($data['type_root']) ?  array() : category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$data['type_root']);    
    $styles = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['style']); 
    $budgets = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['budget']);
    $_GET['t'] = empty($_GET['t']) ? 0 : (int)$_GET['t'];
    $maxNum = import::getCfg('biddingSetting');
    $maxNum = empty($maxNum) ? 5 : (int)$maxNum;
    import::getMdl('company');
    $companies = companyMdl::getInstance()->getCompanyList(array('company_name','uid','logo'),
                                                           array('vip'=>true),
                                                           array('uid'=>'ASC'),
                                                           0,30 );
    shuffle($companies);
    require TEMPLATE_PATH.'tenders_added.html';
    die;
}


if($_GET['act'] === 'ruanzhuang' /*'yanfang'*/){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_ruanzhuang.html';
    die;
}

if($_GET['act'] === 'liangfang'){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_liangfang.html';
    die;
}

if($_GET['act'] === 'sheji'){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_sheji.html';
    die;
}

if($_GET['act'] === 'baojia'){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_baojia.html';
    die;
}





if($_GET['act'] === 'fangan'){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_fangan.html';
    die;
}

if($_GET['act'] === 'jianli'){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_jianli.html';
    die;
}



if($_GET['act'] === 'baozhang'){
    import::getMdl('biddingQuick');
    $total2 = biddingQuickMdl::getInstance()->getBiddingQuickCount(array());
    import::getMdl('area');
    $areas =areaInt::getInstance()->getAreas();
    $types = category::getInstance()->getChildCol($__CATEGORY_TYPE['bidding'],$__BIDDING_ROOT['type']);
    require TEMPLATE_PATH.'tenders_baozhang.html';
    die;
}

if($_GET['act'] === 'setRw'){
    $uid = (int)getUid();
    if(empty($uid)) errorAlert ('请登陆后再试！');
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id']; 
    import::getMdl('biddingBid');
    $bidinfo = biddingBidMdl::getInstance()->getBiddingBid($id);
    if(empty($bidinfo)) errorAlert ('没有该竞标信息');
    $bidding_id = $bidinfo['bid'];
    import::getMdl('bidding');
    $biddingInfo = biddingMdl::getInstance()->getBidding($bidding_id);
    if(empty($biddingInfo)) errorAlert ('没有该招标信息！');
    if((int)$biddingInfo['uid'] !== $uid) errorAlert ('请不要试图越权处理！');
    if(!empty($biddingInfo['bid_id'])) errorAlert ('您的招标信息已经结束不可在继续操作了！');
    $info = array(
        'is_shortlisted' => 1
    );
    if( biddingBidMdl::getInstance()->updateBiddingBid($id,$info)){
        import::getMdl('biddingLook');
        biddingLookMdl::getInstance()->replaceBiddingLook(array('uid'=>$bidinfo['uid'],'bidding_id'=>$bidding_id,'type'=>1));
        dieJs ('alert("操作成功!");parent.location="'.mkUrl::linkTo('tenders','detail',array('id'=>$bidding_id)).'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'setWin'){
    $uid = (int)getUid();
    if(empty($uid)) errorAlert ('请登陆后再试！');
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id']; 
    import::getMdl('biddingBid');
    $bidinfo = biddingBidMdl::getInstance()->getBiddingBid($id);
    if(empty($bidinfo)) errorAlert ('没有该竞标信息');
    $bidding_id = $bidinfo['bid'];
    import::getMdl('bidding');
    $biddingInfo = biddingMdl::getInstance()->getBidding($bidding_id);
    if(empty($biddingInfo)) errorAlert ('没有该招标信息！');
    if((int)$biddingInfo['uid'] !== $uid) errorAlert ('请不要试图越权处理！');
    if(!empty($biddingInfo['bid_id'])) errorAlert ('您的招标信息已经结束不可在继续操作了！');
    $info = array(
        'is_win' => 1
    );
    if( biddingBidMdl::getInstance()->updateBiddingBid($id,$info)){
        biddingMdl::getInstance()->updateBidding($bidding_id,array('bid_id'=>$id));
        import::getMdl('biddingLook');
        biddingLookMdl::getInstance()->replaceBiddingLook(array('uid'=>$bidinfo['uid'],'bidding_id'=>$bidding_id,'type'=>1));
        dieJs ('alert("操作成功!");parent.location="'.mkUrl::linkTo('tenders','detail',array('id'=>$bidding_id)).'"');
    }
    errorAlert('操作失败');
    die;
}