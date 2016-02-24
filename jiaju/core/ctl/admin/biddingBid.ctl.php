<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
define('PAGE_SIZE',30);//分页大小
import::getMdl('biddingBid');
if($_GET['act'] === 'main'){
    $bid =  empty($_GET['bid']) ? errorAlert('没有招标ID') : (int)$_GET['bid'];
    import::getMdl('bidding');
    $data = biddingMdl::getInstance()->getBidding($bid);
    $url = 'index.php?ctl=biddingBid&act=main&bid='.$bid; 
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'],ENT_QUOTES,'UTF-8');
    $where  = array(
        'bid' => $bid
    );
    $totalnum = biddingBidMdl::getInstance()->getBiddingBidCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');  
    $col = array('a.*','b.`username`');     
    $datas = biddingBidMdl::getInstance()->getBiddingBidList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage($url.'&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了竞标列表');
    require TEMPLATE_PATH.'biddingBid/main.html';
    die;
}

if($_GET['act'] === 'add'){
    import::getMdl('bidding');
    $bid =  empty($_GET['bid']) ? errorAlert('没有招标ID') : (int)$_GET['bid'];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['bid'] =$bid;
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(empty($info['uid'])) errorAlert('投标人ID不能为空');
        $info['main_material'] =empty($_POST['main_material']) ? 0: (int)($_POST['main_material']*100);
        if(empty($info['main_material'])) errorAlert('主材料不能为空');
        $info['vice_material'] =empty($_POST['vice_material']) ? 0: (int)($_POST['vice_material']*100);
        if(empty($info['vice_material'])) errorAlert('辅材料不能为空');
        $info['artificial'] =empty($_POST['artificial']) ? 0: (int)($_POST['artificial']*100);
        if(empty($info['artificial'])) errorAlert('人工不能为空');
        $info['management'] =empty($_POST['management']) ? 0: (int)($_POST['management']*100);
        if(empty($info['management'])) errorAlert('管理不能为空');
        $info['design'] =empty($_POST['design']) ? 0: (int)($_POST['design']*100);
        if(empty($info['design'])) errorAlert('设计不能为空');
        $info['detail_pics'] = '';
        $info['message'] = empty($_POST['message']) ? '': trim(htmlspecialchars($_POST['message'],ENT_QUOTES,'UTF-8'));
        $info['total'] = $info['main_material'] + $info['vice_material'] + $info['artificial'] +$info['management'] +  $info['design'];
        $info['t'] = NOWTIME;
        if(empty($info['message'])) errorAlert('留言不能为空');

        try{
            import::getLib('uploadimg');
            $detail = uploadImg::getInstance()->upload('details');
            if(!empty($detail['web_file_name'])) $info['detail_pics'] = json_encode ($detail['web_file_name']);
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!biddingBidMdl::getInstance()->addBiddingBid($info)) errorAlert ('操作失败');
        $count = biddingBidMdl::getInstance()->getBiddingBidCount(array('bid'=>$bid));
        biddingMdl::getInstance()->updateBidding($bid,array('bid_num'=>$count));
        logsInt::getInstance()->systemLogs('新增了竞标列表',$info,array());
        echoJs("alert('操作成功');parent.location='index.php?ctl=biddingBid&act=add&bid=".$bid."'");
        die;
    } 

    require TEMPLATE_PATH.'biddingBid/add.html';
    die;
}

if($_GET['act'] === 'edit'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingBidMdl::getInstance()->getBiddingBid($id);    
    if(empty($data)) errorAlert ('参数出错');
    $data['detail_pics'] = json_decode($data['detail_pics'],true);
    $data['detail_pics'] = empty($data['detail_pics']) ?  array() : $data['detail_pics'];
    $bid = $data['bid'];
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['bid'] =$bid;
        $info['uid'] =empty($_POST['uid']) ? 0: (int)$_POST['uid'];
        $info['is_show'] =empty($_POST['is_show']) ? 0: (int)$_POST['is_show'];
        if(empty($info['uid'])) errorAlert('投标人ID不能为空');
        $info['main_material'] =empty($_POST['main_material']) ? 0: (int)($_POST['main_material']*100);
        if(empty($info['main_material'])) errorAlert('主材料不能为空');
        $info['vice_material'] =empty($_POST['vice_material']) ? 0: (int)($_POST['vice_material']*100);
        if(empty($info['vice_material'])) errorAlert('辅材料不能为空');
        $info['artificial'] =empty($_POST['artificial']) ? 0: (int)($_POST['artificial']*100);
        if(empty($info['artificial'])) errorAlert('人工不能为空');
        $info['management'] =empty($_POST['management']) ? 0: (int)($_POST['management']*100);
        if(empty($info['management'])) errorAlert('管理不能为空');
        $info['design'] =empty($_POST['design']) ? 0: (int)($_POST['design']*100);
        if(empty($info['design'])) errorAlert('设计不能为空');
        $info['detail_pics'] = $data['detail_pics'];
        $info['message'] = empty($_POST['message']) ? '': trim(htmlspecialchars($_POST['message'],ENT_QUOTES,'UTF-8'));
        $info['total'] = $info['main_material'] + $info['vice_material'] + $info['artificial'] +$info['management'] +  $info['design'];
        $info['t'] = NOWTIME;
        if(empty($info['message'])) errorAlert('留言不能为空');
        $detail_pics = array();
        $delpics = array();
        try{
            import::getLib('uploadimg');
            $detail = uploadImg::getInstance()->upload('details');
            if(!empty($detail['web_file_name'])) $detail_pics = $detail['web_file_name'];
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $oldpics = empty($_POST['oldpics']) ? array() : $_POST['oldpics'];
        foreach( $data['detail_pics'] as $k=>$v){
            if(!in_array($v,$oldpics)){
               $delpics[] = $v;
               unset($data['detail_pics'][$k]);
            }
        }

        $info['detail_pics'] = json_encode(array_merge($data['detail_pics'] , $detail_pics));
        if(false === biddingBidMdl::getInstance()->updateBiddingBid($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        logsInt::getInstance()->systemLogs('编辑了竞标信息',$data,$info);
        echoJs("alert('操作成功');parent.location='index.php?ctl=biddingBid&act=edit&id=".$id."'");
        die;
    } 

    require TEMPLATE_PATH.'biddingBid/edit.html';
    die;
        
}

if($_GET['act'] === 'del'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = biddingBidMdl::getInstance()->getBiddingBid($id);    
    if(empty($data)) errorAlert ('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=biddingBid&bid='.$data['bid'] : $_GET['back_url'];
    if(false !== biddingBidMdl::getInstance()->delBiddingBid($id)) {
        logsInt::getInstance()->systemLogs('删除了竞标信息',$data,array());
        $data['detail_pics'] = json_decode($data['detail_pics'],true);
        foreach($data['detail_pics'] as $v){
             if(file_exists(BASE_PATH.$v)) unlink(BASE_PATH.$v);
        }
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'setWin'){
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id']; 
    import::getMdl('biddingBid');
    $bidinfo = biddingBidMdl::getInstance()->getBiddingBid($id);
    if(empty($bidinfo)) errorAlert ('没有该竞标信息');
    $bidding_id = $bidinfo['bid'];
    import::getMdl('bidding');
    $biddingInfo = biddingMdl::getInstance()->getBidding($bidding_id);
    if(empty($biddingInfo)) errorAlert ('没有该招标信息！');
    if(!empty($biddingInfo['bid_id'])) errorAlert ('您的招标信息已经结束不可在继续操作了！');
    $info = array(
        'is_win' => 1
    );
     $back_url = empty($_GET['back_url']) ? 'index.php?ctl=biddingBid&bid='.$bidding_id : $_GET['back_url'];
    if( biddingBidMdl::getInstance()->updateBiddingBid($id,$info)){ 
        logsInt::getInstance()->systemLogs('设置了中标信息',$bidinfo,$info);
        biddingMdl::getInstance()->updateBidding($bidding_id,array('bid_id'=>$id));
        import::getMdl('biddingLook');
        biddingLookMdl::getInstance()->replaceBiddingLook(array('uid'=>$bidinfo['uid'],'bidding_id'=>$bidding_id,'type'=>1));
       dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
   
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'setRw'){
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id']; 
    import::getMdl('biddingBid');
    $bidinfo = biddingBidMdl::getInstance()->getBiddingBid($id);
    if(empty($bidinfo)) errorAlert ('没有该竞标信息');
    $bidding_id = $bidinfo['bid'];
    import::getMdl('bidding');
    $biddingInfo = biddingMdl::getInstance()->getBidding($bidding_id);
    if(empty($biddingInfo)) errorAlert ('没有该招标信息！');
    if(!empty($biddingInfo['bid_id'])) errorAlert ('您的招标信息已经结束不可在继续操作了！');
    $info = array(
        'is_shortlisted' => 1
    );
     $back_url = empty($_GET['back_url']) ? 'index.php?ctl=biddingBid&bid='.$bidding_id : $_GET['back_url'];
    if( biddingBidMdl::getInstance()->updateBiddingBid($id,$info)){
        logsInt::getInstance()->systemLogs('设置了竞标入围',$bidinfo,$info);
        import::getMdl('biddingLook');
        biddingLookMdl::getInstance()->replaceBiddingLook(array('uid'=>$bidinfo['uid'],'bidding_id'=>$bidding_id,'type'=>1));
        dieJs('alert("操作成功");parent.location="'.$back_url.'"');
    }
    errorAlert('操作失败');
    die;
}
