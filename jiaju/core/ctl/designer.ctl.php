<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',6);
import::getMdl('designer');
$id = empty($_GET['id']) ? show404() : (int)$_GET['id'];
$data = designerMdl::getInstance()->getDesigner($id);    
if(empty($data)) show404();   
if(empty($data['face_pic'])) $data['face_pic']  = 'statics/images/130_130.jpg';
$__SETTING['title'] = $data['position'].$data['name'].'的博客';
import::getMdl('company');
$companyname = companyMdl::getInstance()->getCompanyName($data['uid']);
import::getMdl('users');
$is_authentication = usersMdl::getInstance()->checkIsAuthentication($data['uid']);
import::getInt('seo');
$__SETTING = seoInt::getInstance()->load('sjsshow',array('name'=>$data['name'],'position'=>$data['position'],'about'=>$data['about']));
if($_GET['act'] === 'main'){
   
    import::getMdl('case');
    $where = array(
        'designer_id' => $id,
        'is_show' => 1
    );
    $totalnum = caseMdl::getInstance()->getCaseCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('case_id'=>'DESC'); 
    $col = array('`case_id`','`title`','`face_pic`','`style_id`','`price_id`','`area_id`','`pv_num`');
    $datas = caseMdl::getInstance()->getCaseList($col,$where,$orderby,$begin,PAGE_SIZE);
    //$links = createPage(mkUrl::linkTo('designer','main',array('id'=>$id,'page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'designer/index.html';
    die;
}

if($_GET['act'] === 'case'){
    $__SETTING['title'].='--设计作品';
    import::getMdl('case');
    $where = array(
        'designer_id' => $id,
        'is_show' => 1
    );
    $totalnum = caseMdl::getInstance()->getCaseCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $order = empty($_GET['order']) ? 3 : (int)$_GET['order'];
    switch($order){
        case 1:
            $orderby = array('pv_num'=>'DESC'); 
            break;
        case 2:
            $orderby = array('price_id'=>'DESC'); 
            break;
        default:
            $orderby = array('case_id'=>'DESC'); 
            break;
    }
    
    
    $col = array('`case_id`','`title`','`face_pic`','`style_id`','`price_id`','`area_id`','`pv_num`');
    $datas = caseMdl::getInstance()->getCaseList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('designer','case',array('id'=>$id,'page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'designer/case.html';
    die;
}

if($_GET['act'] === 'booking'){
    $__SETTING['title'].='--预约设计';
    import::getMdl('bookingDesign');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['designer_id'] = $id;   
        $info['uid'] = getUid();
        if(empty($info['uid']))  dieJs("parent.ajaxLogin();");
        $info['company_id'] = $data['uid'];
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('电话不能为空');
        $info['date'] = empty($_POST['date']) ? '': trim(htmlspecialchars($_POST['date'],ENT_QUOTES,'UTF-8'));
        if(empty($info['date'])) errorAlert('预约日期不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(empty($info['description'])) errorAlert('亲！您可以补充一些要求或者房屋的一些简单介绍！');
        $info['create_time'] = NOWTIME;
        if(!bookingDesignMdl::getInstance()->addBookingDesign($info)) errorAlert ('操作失败');
        
        import::getInt('sms');
        import::getMdl('users');
        $userinfo = usersMdl::getInstance()->getUsers($data['uid']);
        if(!empty($userinfo['mobile'])){
            smsInt::getInstance()->send('designer',array($userinfo['mobile']),array('name'=>$info['name'],'designer'=>$data['name'],'tel'=> $info['tel'],'site_name'=>$__SETTING['site_name']));
        }
        
        echoJs("alert('预约成功！您预约的设计师在收到您的预约消息后会主动联系您！请耐心等待！');parent.location='".mkUrl::linkTo('designer','main',array('id'=>$id))."'");
        die;
    } 
    require TEMPLATE_PATH.'designer/booking.html';
    die;
}