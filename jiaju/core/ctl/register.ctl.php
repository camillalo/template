<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getMdl('users');
$uid = getUid();
if(!empty($uid)){
    import::getMdl('users');
    $__USER_INFO = usersMdl::getInstance()->getUsers($uid);  
    if(!empty($__USER_INFO)) {
        header("Location: ".URL);
        die;
    }
    setCk ('login_info', '');

}
if($_GET['act'] === 'main'){
    $_GET['type'] = empty($_GET['type']) ?  0 : (int)$_GET['type'];
    switch($_GET['type']){
       case $__USER_TYPE['owner']:
            require TEMPLATE_PATH.'register_'.$_GET['type'].'.html';
            die;
        break;  
        case $__USER_TYPE['company']:    
        case $__USER_TYPE['material']: 
        case $__USER_TYPE['designer']:
 
            require TEMPLATE_PATH.'register_'.$_GET['type'].'.html';
            die;
            break;
           
    }
    require TEMPLATE_PATH.'register.html';
    die;
}

if($_GET['act'] === 'mail'){
    $emailed = false;
    $username = empty($_GET['username']) ? '' : htmlspecialchars($_GET['username'] , ENT_QUOTES,'utf-8');   
    if(!empty($username)){
        $code = empty($_GET['scode']) ?dieJs("alert('验证码不能为空');location.href='".mkUrl::linkTo('register','mail')."'") : strtoupper($_GET['scode']);
        if(empty($code) || $code!== strtoupper(getCk('code'))){
            setCk('code', '');
            dieJs("alert('验证码不正确');location.href='".mkUrl::linkTo('register','mail')."'");
        }
        setCk('code', '');
        $email = empty($_GET['email']) ? dieJs("alert('邮件不能为空');location.href='".mkUrl::linkTo('register','mail')."'") : trim($_GET['email']);
        if(!isEmail($email)) dieJs("alert('请填写正确的邮件地址');location.href='".mkUrl::linkTo('register','mail')."'");
        import::getMdl('users');
        $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
        if(empty($usersInfo))dieJs("alert('用户不存在');location.href='".mkUrl::linkTo('register','mail')."'");
        if($usersInfo['email'] != $email) dieJs("alert('请填写注册时的邮件！');location.href='".mkUrl::linkTo('register','mail')."'");
        if($usersInfo['lock_t'] < NOWTIME) dieJs("alert('已经激活过！');location.href='".mkUrl::linkTo('login')."'");
        $local = explode("@",$email);
        $mailHttp = 'http://mail.'.$local[1];
        import::getInt('sendMail');
        if(sendMailInt::getInstance()->checkAuth('regAuth')){
            sendMailInt::getInstance()->setTitle($__SETTING['site_name']."注册认证");
            $link = mkUrl::linkTo('register','auth',array('sign'=>  authcode($username.'|'.NOWTIME)));
            sendMailInt::getInstance()->setHtmlByT('reg',array('site_name'=>$__SETTING['site_name'],'link'=>$link));
            sendMailInt::getInstance()->send($email);
            $emailed = true;
        }else{
            dieJs("alert('网站暂未开通邮件认证!');location.href='".mkUrl::linkTo('login')."'");
        }
    }
    require TEMPLATE_PATH.'mail.html';
    die;
}

if($_GET['act'] === 'pwd'){
    $emailed = false;
    $username = empty($_GET['username']) ? '' : htmlspecialchars($_GET['username'] , ENT_QUOTES,'utf-8');   
    if(!empty($username)){
        $code = empty($_GET['scode']) ?dieJs("alert('验证码不能为空');location.href='".mkUrl::linkTo('register','pwd')."'") : strtoupper($_GET['scode']);
        if(empty($code) || $code!== strtoupper(getCk('code'))){
            setCk('code', '');
            dieJs("alert('验证码不正确');location.href='".mkUrl::linkTo('register','pwd')."'");
        }
       // setCk('code', '');
        $email = empty($_GET['email']) ? dieJs("alert('邮件不能为空');location.href='".mkUrl::linkTo('register','pwd')."'") : trim($_GET['email']);
        if(!isEmail($email)) dieJs("alert('请填写正确的邮件地址');location.href='".mkUrl::linkTo('register','pwd')."'");
        import::getMdl('users');
        $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
        if(empty($usersInfo))dieJs("alert('用户不存在');location.href='".mkUrl::linkTo('register','pwd')."'");
        if($usersInfo['email'] != $email) dieJs("alert('请填写注册时的邮件！');location.href='".mkUrl::linkTo('register','pwd')."'");
        $local = explode("@",$email);
        $mailHttp = 'http://mail.'.$local[1];
        import::getInt('sendMail');
        if(sendMailInt::getInstance()->checkAuth('pwdAuth')){
            sendMailInt::getInstance()->setTitle($__SETTING['site_name']."找回密码");
            $link = mkUrl::linkTo('register','auth2',array('sign'=>  authcode($username.'|'.NOWTIME)));
            sendMailInt::getInstance()->setHtmlByT('pwd',array('site_name'=>$__SETTING['site_name'],'link'=>$link));
            sendMailInt::getInstance()->send($email);
            $emailed = true;
        }else{
            dieJs("alert('网站暂未开通邮件找回密码功能!');location.href='".mkUrl::linkTo('login')."'");
        }
    }
    require TEMPLATE_PATH.'pwd.html';
    die;
}


if($_GET['act'] === 'auth2'){
    $linkSign=$sign = empty($_GET['sign']) ? errorAlert('无效') : getValue($_GET['sign']);
    $sign = authcode($sign,'DECODE');
    list($username,$time) = explode('|',$sign);
    if(empty($username))        errorAlert('无效访问');
    if($time + 3600 < NOWTIME) errorAlert('该链接已经失效！');
    $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
    if(empty($usersInfo)) errorAlert('用户不存在！');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $pwd = empty($_POST['pwd']) ? errorAlert('新密码不能为空'): getValue($_POST['pwd']);
        $pwd2 = empty($_POST['pwd2']) ? errorAlert('请确认新密码') : md5($_POST['pwd2']);
        if(md5($pwd)!=$pwd2) errorAlert ('两次密码不一致');
        import::getInt('ucenter');
        $ret = ucenterInt::getInstance()->edit2($username,'',$pwd);
        if($ret !== false){
            switch ($ret) {
                    case 0: errorAlert('没做任何修改#002');
                    case -1:  errorAlert ('旧密码不正确#002');                      
                    case -4:  errorAlert( 'Email 格式有误');      
                    case -5:  errorAlert('Email 不允许注册'); 
                    case -6:  errorAlert('该 Email 已经被注册');  
                    case -7:  errorAlert('没有做任何修改');
                    case -8:  errorAlert('该用户受保护无权限更改');
            }
        } 
        $info['password'] = md5($pwd);
       if (false === usersMdl::getInstance()->updateUsers($usersInfo['uid'], $info))
            errorAlert('操作失败');

        import::getMdl('outtoin'); //忽略空结果集 只要更新即可
        outtoinMdl::getInstance()->updateOuttoinByUid($usersInfo['uid'],array('password'=>  authcode($pwd)));

        echoJs('alert("操作成功");parent.location="'.mkUrl::linkTo('login').'"');
        die;
    }
    $link = mkUrl::linkTo('register','auth2',array('sign'=>$linkSign));
    require TEMPLATE_PATH.'pwd_auth2.html';
    die;
}



//账号激活回调
if($_GET['act'] === 'auth'){
    $sign = empty($_GET['sign']) ? die('无效') : $_GET['sign'];
    $sign = authcode($sign,'DECODE');
    list($username,$time) = explode('|',$sign);
    if(empty($username)) die('无效访问');
    if($time + 3600 < NOWTIME) die('该链接已经失效！');
    $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
    if(empty($usersInfo)) die('用户不存在！');
    $info['lock_t'] = 0;
    usersMdl::getInstance()->updateUsers($usersInfo['uid'],$info);
    dieJs("alert('恭喜您账户已经激活');location.href='".mkUrl::linkTo('login')."'");
    die;
}


if($_GET['act'] === 'save'){
    
    $code = empty($_POST['scode']) ? dieJs('parent.showErr("验证码不能为空");') : strtoupper($_POST['scode']);
    if(empty($code) || $code!== strtoupper(getCk('code'))){
        setCk('code', '');
        dieJs('parent.showErr("验证码不正确");parent.clickCode();');
    }
    setCk('code', '');
    
    $data['type'] = empty($_POST['type']) ? dieJs('parent.showErr("请选择您要注册的账户类型");parent.clickCode();') : (int)$_POST['type'];
   
    $name2 = '真实姓名';
    $info = array();
    switch ($data['type']){
        
        case $__USER_TYPE['company']:   
        case $__USER_TYPE['material']:
           
            $name2 ='公司简称';
        
            $info['company_name'] = empty($_POST['company_name']) ? '': trim(htmlspecialchars($_POST['company_name'],ENT_QUOTES,'UTF-8'));
            if(empty($info['company_name'])) dieJs('parent.showErr("公司名称不能为空");parent.clickCode();') ;
            
            break;
        case $__USER_TYPE['designer']:
        case $__USER_TYPE['owner']: break;
        default:
            dieJs('parent.showErr("请选择您要注册的账户类型");parent.clickCode();') ;
            break;
    }
    
    
    $data['username'] = empty($_POST['username']) ? dieJs('parent.showErr("登陆账号不能为空");parent.clickCode();') : htmlspecialchars($_POST['username'],ENT_QUOTES,'utf-8');
    if(is_numeric($data['username']))   dieJs('parent.showErr("登陆账号不允许纯数字");parent.clickCode();');
    if(strlen($data['username'])<6||  strlen($data['username']) > 15) dieJs('parent.showErr("登陆账号长度应在6-15位之间");parent.clickCode();'); //妹的为了满足UC
    if(strtolower(substr($data['username'],0,2)) == 'qq' )  dieJs('parent.showErr("QQ是系统保留字段请勿使用");parent.clickCode();'); //妹的为了满足QQ
    if(strtolower(substr($data['username'],0,4)) == 'open' )  dieJs('parent.showErr("open是系统保留字段请勿使用");parent.clickCode();'); //说不定以后还有什么
    if(strtolower(substr($data['username'],0,4)) == 'sina' )  dieJs('parent.showErr("sina是系统保留字段请勿使用");parent.clickCode();'); //说不定以后还有什么
    
    if(usersMdl::getInstance()->getUsersByUsername($data['username']))dieJs('parent.showErr("登陆账号已经存在");parent.clickCode();');

 
    $data['realname'] = empty($_POST['realname']) ? dieJs('parent.showErr("'.$name2.'不能为空");parent.clickCode();') : htmlspecialchars($_POST['realname'],ENT_QUOTES,'utf-8');
    $data['mobile'] = '';
    if(!empty($_POST['mobile'])){
        $data['mobile'] =  $_POST['mobile'];
        if(!isMobile($data['mobile'])) dieJs('parent.showErr("mobile 格式不正确");parent.clickCode();');
    }
    $data['email'] = empty($_POST['email']) ? dieJs('parent.showErr("email不能为空");parent.clickCode();') : htmlspecialchars($_POST['email'],ENT_QUOTES,'utf-8');
    if(!isEmail($data['email']))dieJs('parent.showErr("email格式不正确");parent.clickCode();') ;
    if(usersMdl::getInstance()->checkEmail($data['email'])) dieJs('parent.showErr("email已存在!");parent.clickCode();') ;
    
    
    $data['sex'] = empty($_POST['sex']) ? 1 : (int)$_POST['sex'];
    $data['password'] = empty($_POST['password']) ? dieJs('parent.showErr("密码不能为空");parent.clickCode();') : md5($_POST['password']);
    $password2 = empty($_POST['password2']) ? dieJs('parent.showErr("请确认密码");parent.clickCode();') : md5($_POST['password2']);
    if($data['password']!=$password2) dieJs('parent.showErr("两次密码不一致");parent.clickCode();');
    $data['reg_t']  = NOWTIME;
    $data['reg_ip'] = getIp();
    
    import::getInt('ucenter');
    $ret = ucenterInt::getInstance()->register($data['username'],$_POST['password'],$data['email']);
    if($ret !== false){
        if(!is_numeric($ret))  dieJs('parent.showErr("'.$ret.'");parent.clickCode();');
        $data['uid'] = $ret;
    }

  
    import::getInt('sendMail');
    if(sendMailInt::getInstance()->checkAuth('regAuth')){
        $data['lock_t'] = NOWTIME+20*86400; //开启邮件验证需要激活
    }
      //die('11111');
    $uid = usersMdl::getInstance()->addUsers($data);
    if(!$uid) dieJs('parent.showErr("注册失败");parent.clickCode();');
    
    //公司注册
    if($data['type'] === $__USER_TYPE['company'] || $data['type'] === $__USER_TYPE['material']){
        $info['uid'] = $uid;
        $info['type'] = $data['type'];
        import::getMdl('company');
        if(!companyMdl::getInstance()->addCompany($info)) errorAlert ('公司商铺开通失败但是您的账号可以继续使用');
    }
   

    dieJs('alert("注册成功!");parent.location="'.mkUrl::linkTo('login').'";');
    die;
}

if($_GET['act'] === 'uname'){
    $username = empty($_GET['username']) ? dieJsonErr('账号不能为空') : trim($_GET['username']);
    if(strlen($username)<6||  strlen($username) > 32) dieJsonErr ('账号长度应在6-32位之间');
    if(usersMdl::getInstance()->getUsersByUsername($username)) dieJsonErr ('账号已经存在');
    dieJsonRight('账号可以使用');
    die;
}
