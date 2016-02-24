<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}



if($_GET['act'] === 'main'){
    session_write_close();
    require  TEMPLATE_PATH.'login.html';
    die;
}

if($_GET['act'] === 'login'){
    
    $code = isset($_POST['code']) ? strtolower(trim($_POST['code'])) : errorAlert('验证码不能为空');
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : errorAlert('用户名不能为空');
    
    $password = isset($_POST['password']) ? md5(trim($_POST['password'])) :  errorAlert('密码不能为空');
    
    if(!isset($_SESSION['scode'])) errorAlert ('访问出错');
    
    if($code !== strtolower($_SESSION['scode'])) {
        echoJs("parent.changeCode();alert('验证码不正确');");
        die;
    }
    unset($_SESSION['scode']);
    
    if(!authManager::getInstance()->login($username,$password)) {
        
        echoJs("parent.changeCode();alert('用户名或密码不正确');");
        die;
    }
    logsInt::getInstance()->systemLogs('登陆了后台');
    echoJs('alert("登陆成功");parent.location="index.php";');
    die;
}

if($_GET['act'] === 'logout'){
	unset($_SESSION['admin']);
	$_SESSION['admin'] = array();
	setcookie(session_name(),"",time()-1); 
    session_destroy();
    session_write_close();
    echoJs('parent.location="index.php";');
    die;
}