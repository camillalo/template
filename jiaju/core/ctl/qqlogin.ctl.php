<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
if($_GET['act'] === 'login'){
    $uid = md5(uniqid(rand(), TRUE));
    setCk('qqstate', $uid);
    $login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
        . QQ_APPID . "&redirect_uri=" . urlencode(mkUrl::linkTo('qqlogin','callback'))
        . "&state=" .$uid
        . "&scope=get_user_info";
    header("Location:$login_url");
    die;
}

if($_GET['act'] === 'callback'){
    
    $id = getCk('qqstate');
    import::getLib('curl');
    $_REQUEST['state'] = empty($_REQUEST['state']) ?  '' : $_REQUEST['state'];
    $_REQUEST['code']  = empty($_REQUEST['code'])  ?  '' : $_REQUEST['code'];
    if($id == $_REQUEST['state']){
        $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" .QQ_APPID. "&redirect_uri=" . urlencode(mkUrl::linkTo('qqlogin','callback'))
            . "&client_secret=" . QQ_KEY. "&code=" . $_REQUEST["code"];
        
        $curl = new cURL();
        $response = $curl->get($token_url);
        if (strpos($response, "callback") !== false)
        {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $params = json_decode($response,true);
        }else{
            $local = explode('&', $response);
            foreach($local as $val){
                if(strpos($val, "access_token") !== false){
                    $local2 = explode('=',$val);
                    $params['access_token'] = trim($local2[1]);
                    break;
                }
            }
        }

        if(empty($params['access_token'])) die('第三方登录验证失败');
        $graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" 
        . $params["access_token"];
        $str  = $curl->get($graph_url);
       
        if (strpos($str, "callback") !== false)
        {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($str,true);
        if(empty($user)) die('腾讯API返回错误');
        
     
        import::getMdl('outtoin');
        $openinfo = outtoinMdl::getInstance()->getOuttoin('QQV3_'.$user['openid']);
        if(empty($openinfo)){
            $graph_url = 'https://graph.qq.com/user/get_user_info?access_token='.$params["access_token"].'&oauth_consumer_key='.QQ_APPID.'&openid='.$user['openid'];
            $qqInfo  = $curl->get($graph_url);  
            $qqInfo = json_decode($qqInfo,true);
            array('nickname'=>$qqInfo['nickname'],'img'=>$qqInfo['figureurl']);
            $password = random(8);
            $info = array('out'=>'QQV3_'.$user['openid'],'password'=>  authcode($password)); 
            $id = outtoinMdl::getInstance()->addOuttoin($info);
            $username = 'QQV3'.$id;
            $email = $username.'@qq.com';
            $userData = array(
                'username' => $username,
                'password' => md5($password),
                'realname' => $qqInfo['nickname'],
                'email'    => $email, 
                'last_ip'  => getIp()
             );
            import::getInt('ucenter');
            $ret = ucenterInt::getInstance()->register($username,$password,$email);
            if($ret !== false){
                if(!is_numeric($ret))  die('uc整合失败');
                $uid = (int)$ret;
                $userData['uid'] = $uid;
            }
            import::getMdl('users');
            if(!empty($userData['uid'])){
                usersMdl::getInstance()->replaceUsers($userData);
            }else{
                $uid = usersMdl::getInstance()->addUsers($userData);
            }
            setCk('login_info',$uid .'|'.NOWTIME.'|'.$userData['last_ip'],86400);
            echo '<script>location.href="'.mkUrl::linkTo('user','info').'"</script>';
            die;
        }
        
        import::getMdl('users');
        
        $username = 'QQV3'.$openinfo['id'];
        $password = authcode($openinfo['password'],'DECODE');
        import::getInt('ucenter');
        $ret = ucenterInt::getInstance()->login($username,$password);
        $js = isset($ret['js']) ? $ret['js'] : '';
        if($ret === false){
                $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
                if(empty($usersInfo)){
                    $userData = array(
                        'username' => $username,
                        'password' => md5($password),
                        'realname' => $qqInfo['nickname'],
                        'email'    => $email, 
                    );
                    $uid = usersMdl::getInstance()->addUsers($userData);
                }else $uid = $usersInfo['uid'];
        }else{
            if(!is_array($ret)) errorAlert ($ret);
            $uid = $ret['uid'];
            $email = $ret['email'];
            $usersInfo =  usersMdl::getInstance()->getUsersByUsername($username); 
            if(empty($usersInfo)){
                $ret = usersMdl::getInstance()->replaceUsers(array('uid'=>$uid,'username'=>$username,'realname'=>$username,'password'=>$password,'email'=>$email));
                if(false === $ret) errorAlert ('用户同步失败'); 
            }
        }
        $info['last_t'] = NOWTIME;
        $info['last_ip'] = getIp();
        $info['fail_num'] = 0;
        usersMdl::getInstance()->updateUsers($uid,$info);
        setCk('login_info',$uid .'|'.NOWTIME.'|'.$info['last_ip'],86400);
        if(!empty($js)) echo $js;
        echo '<script>location.href="'.mkUrl::linkTo('user','info').'"</script>';
        die;

    }   
    die;
}

