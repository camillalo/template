<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',10);//分页大小
if($_GET['act'] === 'main'){
    $__SETTING['title'] .='积分商城';
    $userinfo = array();
    $uid =(int) getUid();
    if($uid){
        import::getMdl('users');
        $userinfo = usersMdl::getInstance()->getUsers($uid);
        import::getMdl('integral');
        $userinfo['jifen'] = integralMdl::getInstance()->getSumIntegralByUid($uid);
        $userinfo['tx'] = usersMdl::getInstance()->getUserExFacePic($uid);
    }
    
    import::getMdl('integralShop');
    import::getMdl('integralExchange');
    
    $hotProducts = array();
    $ids = integralExchangeMdl::getInstance()->getTopExchange(4); //偷个懒 不做JOIN 查询 另外也不做 IS_SHOW的判断
    foreach($ids as $id){
        $hotProducts[] = integralShopMdl::getInstance()->getIntegralShop($id);
    }
    
    $goods = integralShopMdl::getInstance()->getAllIntegralShop(); //这时候需要判断是否下架了
    
    $duihuan = integralExchangeMdl::getInstance()->getIntegralExchangeList(array('b.username','c.product_name','a.`t`'),array('type'=>0),array('a.id'=>'DESC'),0,10);
    $choujiang = integralExchangeMdl::getInstance()->getIntegralExchangeList(array('b.username','c.product_name','a.`t`'),array('type'=>1),array('a.id'=>'DESC'),0,10);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('jifen');
    require TEMPLATE_PATH.'jifen.html';
    die;
}

if($_GET['act'] === 'exchange'){
    $uid = (int)getUid();
    if(!$uid) dieJsonErr ('请登陆后再试');
    import::getMdl('users');
    $userinfo = usersMdl::getInstance()->getUsers($uid);
    if(empty($userinfo)) dieJsonErr ('请登陆后再试');
    $id = empty($_GET['id']) ? dieJsonErr('参数错误') : (int)$_GET['id'];
    $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
    if(empty($info['name'])) dieJsonErr('联系人不能为空');
    $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
    if(empty($info['tel'])) dieJsonErr('联系方式不能为空');
    $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
    import::getMdl('integralShop');
    $good = integralShopMdl::getInstance()->getIntegralShop($id);
    if(empty($good)) dieJsonErr ('没有该物品');
    if($good['num'] <= 0) dieJsonErr ('该物品已经兑换完');
    if(!$good['is_show']) dieJsonErr ('该物品已经下架');
    
    import::getInt('integral');
    if(false === integralInt::getInstance()->checkUserIntegral($uid,$good['exchange_integral'])) dieJsonErr ('很抱歉您的积分不够！');
    
    $ret = integralInt::getInstance()->useUserIntegral($uid,$good['exchange_integral'],$__INTEGRAL_USED['exchange']);
    if($ret === false) dieJsonErr ('很抱歉您的积分不够！');
    
    $info['uid'] = $uid;
    $info['product_id'] = $id;
    $info['type'] = 0;
    $info['integral'] = $good['exchange_integral'];
    $info['t'] = NOWTIME;
    $info['status'] = 0;
    import::getMdl('integralExchange');
    $ret = integralExchangeMdl::getInstance()->addIntegralExchange($info);
    dieJsonRight('兑换成功');
}


if($_GET['act'] === 'lottery'){
    $uid = (int)getUid();
    if(!$uid) dieJsonErr ('请登陆后再试');
    import::getMdl('users');
    $userinfo = usersMdl::getInstance()->getUsers($uid);
    if(empty($userinfo)) dieJsonErr ('请登陆后再试');
    $id = empty($_GET['id']) ? dieJsonErr('参数错误') : (int)$_GET['id'];
    $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
    if(empty($info['name'])) dieJsonErr('联系人不能为空');
    $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
    if(empty($info['tel'])) dieJsonErr('联系方式不能为空');
    $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
    import::getMdl('integralShop');
    $good = integralShopMdl::getInstance()->getIntegralShop($id);
    if(empty($good)) dieJsonErr ('没有该物品');
    if($good['num'] <= 0) dieJsonErr ('该物品已经抽奖完');
    if(!$good['is_show']) dieJsonErr ('该物品已经下架');
    
    import::getInt('integral');
    if(false === integralInt::getInstance()->checkUserIntegral($uid,$good['lottery_integral'])) dieJsonErr ('很抱歉您的积分不够！');
    
    $ret = integralInt::getInstance()->useUserIntegral($uid,$good['lottery_integral'],$__INTEGRAL_USED['lottery']);
    if($ret === false) dieJsonErr ('很抱歉您的积分不够！');
    $rand = rand(0,$good['lottery_probability']);
    if($rand < 2){
        $info['uid'] = $uid;
        $info['product_id'] = $id;
        $info['type'] = 0;
        $info['integral'] = $good['exchange_integral'];
        $info['t'] = NOWTIME;
        $info['status'] = 0;
        import::getMdl('integralExchange');
        $ret = integralExchangeMdl::getInstance()->addIntegralExchange($info);
        dieJsonRight('恭喜您抽中了“'.$good['product_name'].'”！');
    }
    dieJsonRight('很抱歉您未抽中！');
    
}