<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getMdl('users');
$uid = getUid();
if($_GET['act'] === 'main'){
    if(!empty($uid)){
        import::getMdl('users');
        $__USER_INFO = usersMdl::getInstance()->getUsers($uid);  
        if(!empty($__USER_INFO)) {
            header("Location: ".URL);
            die;
        }
        setCk ('login_info', '');
       
    }
    $back_url = empty($_SERVER['HTTP_REFERER']) ? mkUrl::linkTo('index') : $_SERVER['HTTP_REFERER'];
    require TEMPLATE_PATH.'login.html';
    die;
}

if($_GET['act'] === 'loging'){
    $username = empty($_POST['username']) ? errorAlert('用户名不能为空') : $_POST['username'];
    $_POST['password'] = empty($_POST['password']) ? errorAlert('密码不能为空') : getValue($_POST['password'],true);
    $password = md5($_POST['password']);
    import::getInt('ucenter');
    $ret = ucenterInt::getInstance()->login($username,$_POST['password']);
   // print_r($ret);
   // die;
    $js = isset($ret['js']) ? $ret['js'] : '';
    if($ret === false){
        $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
        if(empty($usersInfo)) errorAlert ('用户名不存在');
        if($usersInfo['lock_t'] > NOWTIME ){
            if( $usersInfo['failnum'] >5){
                errorAlert ('该帐号暂时冻结！请稍后再试！');
            }else{
                dieJs('alert("该帐号需要激活才能使用!");parent.location="'.mkUrl::linkTo('register','mail').'"');
            }
        }
        
        if($usersInfo['password'] != $password){
            $info['fail_num'] = $usersInfo['failnum'] + 1;
            if($info['fail_num'] > 5) $info['lock_t'] = NOWTIME+ 7200;
            usersMdl::getInstance()->updateUsers($usersInfo['uid'],$info);
            errorAlert('用户名或密码不正确');
        }
        $uid = $usersInfo['uid'];
    }else{
        if(!is_array($ret)) errorAlert ($ret);
        $uid = $ret['uid'];
        $email = $ret['email'];
        $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
        
        if(empty($usersInfo)){
            import::getInt('sendMail');
            if(sendMailInt::getInstance()->checkAuth('regAuth')){
                $lock_t = NOWTIME+20*86400; //开启邮件验证需要激活
            }else{
                $lock_t = 0;
            }
            $ret = usersMdl::getInstance()->replaceUsers(array('uid'=>$uid,'username'=>$username,'realname'=>$username,'password'=>$password,'email'=>$email,'lock_t'=>$lock_t));
   
            if(false === $ret) errorAlert ('用户同步失败'); 
            if($lock_t){
                    dieJs('alert("该帐号需要激活才能使用!");parent.location="'.mkUrl::linkTo('register','mail').'"');
            }
        }else{
            if($usersInfo['lock_t'] > NOWTIME ){
                dieJs('alert("该帐号需要激活才能使用!");parent.location="'.mkUrl::linkTo('register','mail').'"'); 
            }

            if(empty($userInfo['realname'])){
                $info['username'] = $username;
                $info['realname'] = $username;
            }
        } 
    }
   
    $info['last_t'] = NOWTIME;
    $info['last_ip'] = getIp();
    $info['fail_num'] = 0;
    usersMdl::getInstance()->updateUsers($uid,$info);
    setCk('login_info',$uid .'|'.NOWTIME.'|'.$info['last_ip'],86400);
    $back_url = empty($_POST['back_url']) ? URL : $_POST['back_url'];
    if(!empty($js)) echo $js;
    echoJs('alert("登录成功");parent.location="'.$back_url.'";');
    die;
}



if($_GET['act'] === 'ajaxLogin'){
    $username = empty($_POST['username']) ? errorAlert('用户名不能为空') : $_POST['username'];
    $password = empty($_POST['password']) ? errorAlert('密码不能为空') : md5($_POST['password']);
    import::getInt('ucenter');
    $ret = ucenterInt::getInstance()->login($username,$_POST['password']);
     $js = isset($ret['js']) ? $ret['js'] : '';
    if($ret === false){
        $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
        if(empty($usersInfo)) errorAlert ('用户名不存在');
        if($usersInfo['lock_t'] > NOWTIME) errorAlert ('该帐号暂时冻结！请稍后再试！');
        if($usersInfo['password'] != $password){
            $info['fail_num'] = $usersInfo['failnum'] + 1;
            if($info['fail_num'] > 5) $info['lock_t'] = NOWTIME+ 7200;
            usersMdl::getInstance()->updateUsers($usersInfo['uid'],$info);
            errorAlert('用户名或密码不正确');
        }
        $uid = $usersInfo['uid'];
    }else{
       if(!is_array($ret)) errorAlert ($ret);
        $uid = $ret['uid'];
        $email = $ret['email'];
        $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
        if(empty($usersInfo)){
            import::getInt('sendMail');
            if(sendMailInt::getInstance()->checkAuth('regAuth')){
                $lock_t = NOWTIME+20*86400; //开启邮件验证需要激活
            }else{
                $lock_t = 0;
            }
            $ret = usersMdl::getInstance()->replaceUsers(array('uid'=>$uid,'username'=>$username,'realname'=>$username,'password'=>$password,'email'=>$email,'lock_t'=>$lock_t));
            if(false === $ret) errorAlert ('用户同步失败'); 
            if($lock_t){
                    dieJs('alert("该帐号需要激活才能使用!");parent.location="'.mkUrl::linkTo('register','mail').'"');
            }
        }else{
            if($usersInfo['lock_t'] > NOWTIME ){
                dieJs('alert("该帐号需要激活才能使用!");parent.location="'.mkUrl::linkTo('register','mail').'"'); 
            }
            if(empty($userInfo['realname'])){
                $info['username'] = $username;
                $info['realname'] = $username;
            }
        } 
    }
    $info['last_t'] = NOWTIME;
    $info['last_ip'] = getIp();
    $info['fail_num'] = 0;
    usersMdl::getInstance()->updateUsers($uid,$info);
    setCk('login_info', $uid.'|'.NOWTIME.'|'.$info['last_ip'],86400);
    $back_url = empty($_POST['back_url']) ? '' : $_POST['back_url'];
     if(!empty($js)) echo $js;
    echoJs('alert("操作成功");self.location.href="'.$back_url.'";');
    die;
}

if($_GET['act'] === 'logout'){
    setCk('login_info','');
    setCk('openinfo','');
    setCk('openid', '');
    import::getInt('ucenter');
    echo ucenterInt::getInstance()->logout();
    echoJs('alert("退出登录成功");parent.location="'. mkUrl::linkTo('index').'"');
    die;
}