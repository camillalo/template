<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
if($_GET['act'] === 'main'){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = empty($_POST['data']) ? errorAlert('内容不能为空'):$_POST['data'];
        foreach($data as $key =>$val){
            $data[$key] = trim($val);
        }
        $data['qq'] = preg_split('/[^0-9]+/',$data['qq']);
        makeCfg('setting',$data);
        logsInt::getInstance()->systemLogs('编辑了站点配置',$__SETTING,$data);
        echoJs('alert("操作成功");parent.location="index.php?ctl=site";');
        die;
    }
    logsInt::getInstance()->systemLogs('查看了站点配置');
    require TEMPLATE_PATH.'site/main.html';
    die;
}
if($_GET['act'] === 'authority'){
        $data = import::getCfg('authority');
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info= empty($_POST['info']) ? errorAlert('内容不能为空'):$_POST['info'];
        makeCfg('authority',$info);
        logsInt::getInstance()->systemLogs('编辑了会员权限设置',$data,$info);
        echoJs('alert("操作成功");parent.location="index.php?ctl=site&act=authority";');
        die;
    }
    
    logsInt::getInstance()->systemLogs('查看了会员权限设置');
    require TEMPLATE_PATH.'site/authority.html';
    die;
}

if($_GET['act'] === 'uc'){
     $_UC_SETTING = import::getCfg('ucSetting');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = empty($_POST['data'] ) ? array() : $_POST['data'];
        foreach($data as $key =>$val){
            $data[$key] = trim($val);
        }
        makeCfg('ucSetting',$data);
        logsInt::getInstance()->systemLogs('编辑了UC整合设置',$_UC_SETTING,$data);
        echoJs('alert("操作成功");parent.location="index.php?ctl=site&act=uc";');
        die;
    }
   
    logsInt::getInstance()->systemLogs('查看了UC整合设置');
    require TEMPLATE_PATH.'site/uc.html';
    die;
}

if($_GET['act'] === 'mail'){
    $mail = import::getCfg('mail');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = empty($_POST['data']) ? array() : $_POST['data'];
        foreach($data as $key =>$val){
            $data[$key] = trim($val);
        }
        makeCfg('mail', $data);
         logsInt::getInstance()->systemLogs('编辑了EMAIL设置',$mail,$data);
        echoJs('alert("操作成功");parent.location="index.php?ctl=site&act=mail";');
        die;
    }
    logsInt::getInstance()->systemLogs('查看了EMAIL设置');
    require TEMPLATE_PATH .'site/mail.html';
    die;
}

if($_GET['act'] === 'sms'){
    $sms = import::getCfg('sms');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = empty($_POST['data']) ? array() : $_POST['data'];
        foreach($data as $key =>$val){
            $data[$key] = trim($val);
        }
        makeCfg('sms', $data);
        logsInt::getInstance()->systemLogs('编辑了短信设置',$sms,$data);
        echoJs('alert("操作成功");parent.location="index.php?ctl=site&act=sms";');
        die;
    }
    logsInt::getInstance()->systemLogs('查看了短信设置');
    require TEMPLATE_PATH .'site/sms.html';  
    die;
}


if($_GET['act'] === 'watermark'){
    $watermark = import::getCfg('watermark');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data['type']  = empty($_POST['type']) ? errorAlert('请选择类型') :htmlspecialchars($_POST['type'],ENT_QUOTES,'UTF-8'); 
        if($data['type']  == 'word'){
            $data['word']  = empty($_POST['word']) ? errorAlert('文字水印不能为空') :htmlspecialchars($_POST['word'],ENT_QUOTES,'UTF-8'); 
        }else{
            try{
                import::getLib('uploadimg');
                if(!empty($_FILES['pic']['tmp_name'])){ 
                    $pic = uploadImg::getInstance()->upload('pic');
                    if(!empty($pic['web_file_name'])) $data['pic'] = $pic['web_file_name'];
                } 

             }  catch (Exception $e){
                 errorAlert($e->getMessage());
             }
            
        }
        makeCfg('watermark', $data);
        logsInt::getInstance()->systemLogs('编辑了水印设置',$watermark,$data);
        echoJs('alert("操作成功");parent.location="index.php?ctl=site&act=watermark";');
        die;
    }
    logsInt::getInstance()->systemLogs('查看了水印设置');
    require TEMPLATE_PATH .'site/watermark.html';  
    die;
}