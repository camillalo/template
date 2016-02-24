<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',10);//分页大小
import::getMdl('comments');
$uid = getUid();
if($_GET['act'] === 'list'){
    //sleep(5);
    $type =  empty($_GET['type']) ? 0 : (int)$_GET['type'];
    $type_id = empty($_GET['type_id']) ? 0 : (int)$_GET['type_id'];
    $where  = array(
        'is_show' => 1,
        'type'    => $type,
        'type_id' => $type_id
    );
    $url = array(
        'type'   => $type,
        'type_id'=>$type_id,
        'page'   => '%d'
    );
    $totalnum = commentsMdl::getInstance()->getCommentsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('a.`id`','a.`uid`','b.`username`','a.`create_time`','a.`comments`');     
    $datas = commentsMdl::getInstance()->getCommentsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('comments','list',$url), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'comments_list.html';
    die;
}

if($_GET['act'] === 'save'){
    if(empty($uid)) dieJs("parent.ajaxLogin();");
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('comments',$uid)) errorAlert ('您没有权限评论');
    $type =  empty($_GET['type']) ? 0 : (int)$_GET['type'];
    $type_id = empty($_GET['type_id']) ? 0 : (int)$_GET['type_id'];
    $where  = array(
        'uid'     => $uid,  
        'type'    => $type,
        'type_id' => $type_id
    );
    $totalnum = commentsMdl::getInstance()->getCommentsCount($where);
    if($totalnum > 0) errorAlert ('该内容不可重复评论');
    $info = array(
        'uid'       => $uid,
        'type'      => $type,
        'type_id'   => $type_id,
        'create_time' => date('Y-m-d H:i:s',NOWTIME),
        'is_show'   => (int)authorityInt::getInstance()->isShow(),
    );
    
    $info['comments'] = empty($_POST['comments']) ? '': trim(htmlspecialchars($_POST['comments'],ENT_QUOTES,'UTF-8'));
    if(empty($info['comments'])) errorAlert('评论内容不能为空');
    if(!commentsMdl::getInstance()->addComments($info)) errorAlert ('发表失败');
    echoJs('alert("发表成功");parent.ajaxGetComments();');
    die;
}