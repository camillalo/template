<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
import::getMdl('ask');
import::getInt('category');
import::getLib('pscws5');
import::getMdl('keywords');
import::getMdl('askKeywordMaps');
import::getMdl('askAnswer');
import::getMdl('askAdded');
define('PAGE_SIZE', 10);
import::getInt('recommend');
recommend::getInstance()->init(4);
if($_GET['act'] === 'main'){
    $__SETTING['title'] .= '(问答)';
    $col = array('id','title','orderby','num');
    $orderby = array('orderby'=> 'DESC', 'id'=>'DESC');  
    
    $has = askMdl::getInstance()->getAskList($col,array('status'=>1),$orderby,0,PAGE_SIZE);
    $wait =  askMdl::getInstance()->getAskList($col,array('status'=>0),$orderby,0,PAGE_SIZE);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('ask');
    require TEMPLATE_PATH.'ask.html';
    die;
}

if($_GET['act'] === 'add'){
    $uid = getUid();
    if(empty($uid)){
        header("Location: ".mkUrl::linkTo('login'));
        die;
    }
    import::getMdl('users');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
        $info['integral'] = empty($_POST['integral']) ? 0 : (int)$_POST['integral'];
        import::getInt('integral');
        if(!integralInt::getInstance()->checkUserIntegral($uid,$info['integral'])) errorAlert ('您的账户积分不足!');
        $info['title'] = empty($_POST['title']) ? errorAlert('问题不能为空') : getValue($_POST['title'],true);
        $info['description'] = empty($_POST['description']) ? errorAlert('问题描述不能为空') : getValue($_POST['description']);
        $info['create_time'] = NOWTIME;
        $info['ip'] = getIp();
        $info['last_time'] = empty($_POST['enddate']) ? NOWTIME : NOWTIME + (int)$_POST['enddate'] * 86400;
        $info['cate_id'] = empty($_POST['type_id']) ? errorAlert('请选择问题分类') : (int)$_POST['type_id'];
        $ret = integralInt::getInstance()->useUserIntegral($uid,$info['integral'],$__INTEGRAL_USED['ask']);
        if(!$ret) errorAlert ('您的账户积分不足！');
        $ask_id = askMdl::getInstance()->addAsk($info);
        if($ask_id){
            
            $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
            foreach($keywords as $val){
                $mapinfo = array();
                $mapinfo['ask_id'] = $ask_id;
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
                if(!$mapinfo['keyword_id']){
                    $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
                }
                askKeywordMapsMdl::getInstance()->addaskKeywordMaps($mapinfo);
            }
            echoJs('alert("发布问题成功!");parent.location="'.mkUrl::linkTo('ask','detail',array('id'=>$ask_id)).'"');
        }else{
            errorAlert('发布问题失败');
        }
        die;
    }
    $s = empty($_GET['word']) ?  '' : htmlspecialchars($_GET['word'],ENT_QUOTES,'UTF-8');
    require TEMPLATE_PATH.'ask_add.html';
    die;
}

if($_GET['act'] === 'list'){

    $url = array();
    $where  = array();
    $cid = empty($_GET['cid']) ?  0 : (int)$_GET['cid'];
    if(!empty($cid)){
        $where['cate_id'] = $cid;
        $url['cid'] = $cid;
    }
    if(isset($_GET['st'])){
        $where['status'] = (int)$_GET['st'];
        $url['st'] = (int)$_GET['st'];
    }
    
    $totalnum = askMdl::getInstance()->getAskCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=> 'DESC', 'id'=>'DESC');   
    $col = array('`id`','`ip`','`cate_id`','`title`','`description`','`status`');     
    $datas = askMdl::getInstance()->getAskList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('ask','list',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('asklist',array('catename'=> category::getInstance()->getCategoryName($__CATEGORY_TYPE['ask'],$cid) ));
    
    require TEMPLATE_PATH.'ask_list.html';
    die;
}


if($_GET['act']=== 'detail'){
    $id = empty($_GET['id']) ? show404() : (int)$_GET['id'];
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data))        show404();
    $where  = array();
    $url['id'] = $id;
    $where['ask_id'] = $id;
 
    $totalnum = askAnswerMdl::getInstance()->getAskAnswerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');   
    $col = array('a.`id`','b.`username`','a.`uid`','a.`ask_id`','a.`content`','a.`create_time`','a.`ip`');     
    $datas = askAnswerMdl::getInstance()->getAskAnswerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('ask','detail',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    
    $keywords = askKeywordMapsMdl::getInstance()->getAskKeywordCol($id);
    if(!empty($keywords)){
        $askIds = askKeywordMapsMdl::getInstance()->getAskKeywordMapsList($keywords);
        $showDatas = askMdl::getInstance()->getAsksByIds($askIds);   
    }
    askMdl::getInstance()->askPv($id);
    $uid = getUid();
    
    $addeds = askAddedMdl::getInstance()->getAskAddedList(array(),$where,array(),0,PAGE_SIZE);
    
    import::getMdl('users');
    if(!empty($data['answer_id'])){
        $best = askAnswerMdl::getInstance()->getAskAnswer($data['answer_id']);
        $bestname = usersMdl::getInstance()->getUsername($best['uid']);
    }
    $myname = usersMdl::getInstance()->getUsername($data['uid']);
    $__SETTING['title'] = $data['title'];
    
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('asklist',array('title'=>$data['title'] ));
    
    require TEMPLATE_PATH.'ask_detail.html';
    die;
}

if($_GET['act'] === 'answer'){
    $uid = getUid();
    if(empty($uid)) dieJs("parent.ajaxLogin();");
    
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data))       errorAlert('参数错误');
    $info['uid'] = $uid;
    $info['ask_id'] = $id;
    $info['create_time'] = NOWTIME;
    $info['ip'] = getIp();
    $info['content'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
    if(empty($info['content'])) errorAlert('回答内容不能为空');
    if(!askAnswerMdl::getInstance()->addAskAnswer($info)) errorAlert ('操作失败');
    import::getInt('integral');
    integralInt::getInstance()->obtain($__INTEGRAL_GAIN['answer']);
    echoJs('alert("回答成功!");parent.location="'.mkUrl::linkTo('ask','detail',array('id'=>$id)).'"');
    askMdl::getInstance()->askNum($id);
    die;
}

if($_GET['act'] === 'added'){
    $uid = (int)getUid();
    if(empty($uid)) dieJs("parent.ajaxLogin();");
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data))       errorAlert('参数错误');
    if((int)$data['uid'] !== $uid){
        errorAlert('参数错误');
    }
    $info['ask_id'] = $id;
    $info['added'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
    if(!askAddedMdl::getInstance()->addAskAdded($info)) errorAlert ('操作失败');
    echoJs('alert("补充问题成功!");parent.location="'.mkUrl::linkTo('ask','detail',array('id'=>$id)).'"');
    die;
}

if($_GET['act'] === 'yes'){
    $uid = (int)getUid();
    if(empty($uid)) dieJs("parent.ajaxLogin();");
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];
    $data = askMdl::getInstance()->getAsk($id);    
    if(empty($data))       errorAlert('参数错误');
    if((int)$data['uid'] !== $uid){
        errorAlert('参数错误');
    }
    $answer_id =empty($_GET['answer_id']) ? errorAlert('参数错误') : (int)$_GET['answer_id'];
    $answerData = askAnswerMdl::getInstance()->getAskAnswer($answer_id);
    if(empty($answerData))       errorAlert('参数错误');
    if($answerData['ask_id'] != $id) errorAlert ('不可以设定');
    $info['answer_id'] = $answer_id;
    $info['status'] = 1;
    if(!askMdl::getInstance()->updateAsk($id,$info)) errorAlert ('操作失败');
    import::getMdl('users');
  //  if($data['integral']) usersMdl::getInstance()->addIntegral($answerData['uid'],$data['integral']);
    echoJs('alert("设置成功!");parent.location="'.mkUrl::linkTo('ask','detail',array('id'=>$id)).'"');
    die;
}