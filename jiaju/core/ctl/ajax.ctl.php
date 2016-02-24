<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}


if($_GET['act'] === 'getBrand'){
    import::getMdl('brandMap');
    import::getMdl('brand');
    $id = empty($_GET['category_id']) ? dieJsonErr('参数错误!') : (int)$_GET['category_id'];
    $mapids = brandMapMdl::getInstance()->getAllBrandMap($id); 
    $brands  = brandMdl::getInstance()->getBrandsByIds($mapids);
    dieJsonRight($brands);
    die;
}
//跨域的问题 只能在这里请求了 
if($_GET['act'] === 'checkLogin'){
    import::getMdl('users');
    $uid = getUid();
    
    if($uid) {
        $usersInfo = usersMdl::getInstance()->getUsers($uid);
        if(!empty($usersInfo)){
            dieJsonRight(array('type'=>'user','realname'=>$usersInfo['realname']));
        } 
    }else{   
        $openinfo = getCk('openinfo');
        if(!empty($openinfo)){
            $openinfo = json_decode($openinfo,true);
            dieJsonRight($openinfo);
        }
    }
    dieJsonErr(array()); 
    die;
}

//登录成功后回调
if($_GET['act'] === 'loging'){
    echoJs('parent.checkFlush();');
    die;
}


if($_GET['act'] === 'getCategory'){
    import::getInt('category');
    $category_type = empty($_GET['category_type']) ? dieJsonErr('请选择类型') : (int)$_GET['category_type'];
    $parent_id     = empty($_GET['parent_id']) ? 0 : (int)$_GET['parent_id'];
    if($parent_id !==0 ){
        $category_info = category::getInstance()->getCategory($category_type,$parent_id);
        if(empty($category_info)) dieJsonErr ('上级分类不正确');
    }
    $data = category::getInstance()->getChild($category_type,$parent_id);
    dieJsonRight($data);    
    die;
}



if($_GET['act'] === 'siteApply'){
    $info['uid'] = (int)  getUid();
    if(empty($info['uid'])) dieJsonErr ('请登陆后预约');
    $info['site_id'] = empty($_GET['id']) ? dieJsonErr('请选择要预约的工地') : (int)$_GET['id'];
    import::getMdl('buildingSiteApply');
    $num = buildingSiteApplyMdl::getInstance()->getBuildingSiteApplyCount(array('site_id'=>$info['site_id'],'uid'=>$info['uid']));
    if($num) dieJsonErr ('您已经预约过该工地');
    $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
    if(empty($info['name'])) dieJsonErr('您的称呼不能为空');
    $info['phone'] = empty($_POST['phone']) ? '': trim(htmlspecialchars($_POST['phone'],ENT_QUOTES,'UTF-8'));
    if(empty($info['phone'])) dieJsonErr('联系方式不能为空');
    $info['comment'] = empty($_POST['comment']) ? '': trim(htmlspecialchars($_POST['comment'],ENT_QUOTES,'UTF-8'));
    if(empty($info['comment'])) dieJsonErr('描述不能为空');
    $info['create_time'] = NOWTIME;
    if(!buildingSiteApplyMdl::getInstance()->addBuildingSiteApply($info)) dieJsonErr ('操作失败');
    import::getInt('sms');
    import::getMdl('buildingSite');
    $siteInfo = buildingSiteMdl::getInstance()->getBuildingSite($info['site_id']);
     if(!empty($siteInfo['company_id'])){
        import::getMdl('company');
        import::getMdl('users');
        $usersInfo = usersMdl::getInstance()->getUsers($siteInfo['company_id']);
        if(!empty($usersInfo['mobile'])){
           smsInt::getInstance()->send('site',array($usersInfo['mobile']),array('name'=>$info['name'],'company'=> companyMdl::getInstance()->getCompanyName($siteInfo['company_id']),'phone'=> $info['phone'],'site_name'=>$__SETTING['site_name']));
        }
    }
    
    dieJsonRight('预约成功');
    die;
}

if($_GET['act'] === 'upload'){
    //$uid = getUid();
   // if(empty($uid)) die;
    import::getLib('uploadimg');    
    $time = date('YmdH',NOWTIME);
    $watermark = import::getCfg('watermark');
    
    try{
        $return  = uploadImg::getInstance()->upload('Filedata');
        if(!empty($watermark['type'])){
            if($watermark['type'] == 'word'){
                uploadImg::getInstance()->imageWaterMark($return['store_file_name'],'',$watermark['word']);
            }else{
                uploadImg::getInstance()->imageWaterMark($return['store_file_name'],BASE_PATH.$watermark['pic']);
            }
        }
        uploadImg::getInstance()->resizeImage($return['store_file_name'],$return['store_file_name'],800,600);
        $return['store_file_name'] = md5($return['web_file_name'].AUTH_KEY.$time);
        echo join('|||',$return);
    }  catch (Exception $e){
        die('no');
    }
    die;
}
if($_GET['act'] === 'delPic'){
    $uid = getUid();
    if(empty($uid)) die;
    $time = date('YmdH',NOWTIME);
    $pic = empty($_GET['pic']) ? die('*****') : trim($_GET['pic']);
    $token = empty($_GET['token']) ? die('****') : trim($_GET['token']);
    if(empty($token))die('****');
    if($token !== md5($pic.AUTH_KEY.$time)) die('***');
    $pic = BASE_PATH.'/'.$pic;
    if(file_exists($pic)) unlink ($pic);
    die;
}

