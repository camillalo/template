<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',8);//分页大小
import::getMdl('activity');
$__SETTING['title'] .= '团购活动';
if($_GET['act'] === 'main'){
    $url = array();
    $where  = array();
    $_GET['area_id'] = empty($_GET['area_id']) ?  0 : (int)$_GET['area_id'];
    if(!empty($_GET['area_id'])) {
        $url['area_id'] = $_GET['area_id'];
        $where['area_id'] = $_GET['area_id'];
    }
    $_GET['type'] = empty($_GET['type']) ?  0 : (int)$_GET['type'];
    if(!empty($_GET['type'])) {
        $url['type'] = $_GET['type'];
        $where['type'] = $_GET['type'];
    }
    
    $_GET['st'] = empty($_GET['st']) ?  0 : (int)$_GET['st'];
    if(!empty($_GET['st'])) {
        $url['st'] = $_GET['st'];
        $where['st'] = $_GET['st'];
    }
    
    $totalnum = activityMdl::getInstance()->getActivityCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('id'=>'DESC');    
    $col = array('`id`','`title`','`face_pic`','reg_time','bg_time','end_time','coupon','sign_num','addr');     
    $datas = activityMdl::getInstance()->getActivityList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('activity','main',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    $areas = areaInt::getInstance()->getAreas();
    $today = date('Y-m-d',NOWTIME);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('activity');
    require TEMPLATE_PATH.'activity_index.html';
    die;
}

if($_GET['act'] === 'detail'){
    $id = empty($_GET['id']) ? 1 : (int)$_GET['id'];
    $data = activityMdl::getInstance()->getActivity($id);
    if(empty($data)){
        header("Location: ".mkUrl::linkTo('index'));
        die;
    }
    $today = date('Y-m-d',NOWTIME);
    $__SETTING['title'] = $data['title'];
    import::getMdl('activityJoin');
    $joins = activityJoinMdl::getInstance()->getLastJoin($id,10);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('activityshow',array('title'=>$data['title'],'coupon'=>$data['coupon']));
    require TEMPLATE_PATH.'activity_detail.html';
    die;
}


if($_GET['act'] === 'join'){
    import::getMdl('activityJoin');
    $uid = (int)getUid();
    $ip  = getIp();
    $info['activity_id'] =empty($_GET['id']) ? 0: (int)$_GET['id'];
    if(empty($info['activity_id'])) errorAlert('活动ID不能为空');
    $activityInfo = activityMdl::getInstance()->getActivity($info['activity_id']);
    if(empty($activityInfo)) errorAlert ('没有该活动');
    if(activityJoinMdl::getInstance()->checkIp($info['activity_id'],$ip)) errorAlert ('您已经报名过该活动');
    $info['uid'] = $uid;
    $info['ip']  = $ip;
    $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
    if(empty($info['name'])) errorAlert('姓名不能为空');
    $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
    if(empty($info['addr'])) errorAlert('小区名称不能为空');
    $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
    if(empty($info['tel'])) errorAlert('联系方式不能为空');
   // $info['email'] = empty($_POST['email']) ? '': trim(htmlspecialchars($_POST['email'],ENT_QUOTES,'UTF-8'));
   // if(empty($info['email'])) errorAlert('邮件不能为空');
    $info['qq'] = empty($_POST['qq']) ? '': trim(htmlspecialchars($_POST['qq'],ENT_QUOTES,'UTF-8'));
    if(empty($info['qq'])) errorAlert('QQ不能为空');
    $info['num'] =empty($_POST['num']) ? 1 : (int)$_POST['num'];
    if(empty($info['num'])) errorAlert('参加人数不能为空');
    if(!activityJoinMdl::getInstance()->addActivityJoin($info)) errorAlert ('添加失败');
    $update = array(
        'sign_num' => $activityInfo['sign_num'] + $info['num']
    );
    
    import::getInt('sms');
    smsInt::getInstance()->sendToAdmin('tuanadmin',array('name'=>$info['name'],'tel'=>$info['tel'],'title'=>$activityInfo['title']));
    activityMdl::getInstance()->updateActivity($info['activity_id'],$update);
    echoJs('alert("报名成功");parent.location="'.mkUrl::linkTo('activity','detail',array('id'=>$info['activity_id'] )).'"');
    die;
}
