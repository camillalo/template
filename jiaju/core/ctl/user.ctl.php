<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}

$uid = (int) getUid();
if(empty($uid)){
    header("Location: ".mkUrl::linkTo('login'));
    die;
}
import::getMdl('users');
$__SETTING['title'] = '管理中心';
$__USER_INFO = usersMdl::getInstance()->getUsers($uid);  
if(empty($__USER_INFO)) show404();
//替换一下 VIP 等级
if($__USER_INFO['day'] < NOWTIME && empty($__USER_INFO['num']) && empty($__USER_INFO['gold']) ){
    if(!empty($__USER_INFO['rank_id'])) usersMdl::getInstance ()->updateUsers($uid,array('rank_id'=>0));
}
    
define('PAGE_SIZE',10);
if($_GET['act'] === 'main'){
    $userEx = usersMdl::getInstance()->getUsersEx($uid);
    $isVip = usersMdl::getInstance()->checkIsVip($uid);
    import::getMdl('content');
    $newGG = contentMdl::getInstance()->getContentsByCateId($__PINDAO_ROOT['gg'],5);
    import::getMdl('bidding');
    $biddingNum = biddingMdl::getInstance()->getBiddingCount(array('uid'=>$uid));
    import::getMdl('diary');
    $diaryNum = diaryMdl::getInstance()->getDiaryCount(array('uid'=>$uid));
    import::getMdl('case');
    $newCases = caseMdl::getInstance()->getNewCase(12);
    
    $authority = import::getCfg('authority');
    
    import::getMdl('integral');
    $jifen = integralMdl::getInstance()->getSumIntegralByUid($uid);
    
    require TEMPLATE_PATH.'user/main.html';
    die;
}


if($_GET['act'] === 'info'){
    $userEx = usersMdl::getInstance()->getUsersEx($uid);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if (!empty($_POST['password'])) {
            $password2 = empty($_POST['password2']) ? errorAlert('旧密码不能为空') : trim($_POST['password2']);
            
            $password = trim($_POST['password']);
            $info['password'] = md5($password);
        }
        $info['realname'] = empty($_POST['realname']) ? '' : trim(htmlspecialchars($_POST['realname'], ENT_QUOTES, 'UTF-8'));
        $info['email'] = empty($_POST['email']) ? '' : trim(htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'));
        $u = usersMdl::getInstance()->checkEmail($info['email']);
        if($u && (int)$u != $uid) errorAlert ('email已经存在');
        $info['mobile'] = empty($_POST['mobile']) ? '' : trim(htmlspecialchars($_POST['mobile'], ENT_QUOTES, 'UTF-8'));
        $info['sex'] = empty($_POST['sex']) ? 0 : (int) $_POST['sex'];
        $info['type'] = empty($_POST['type']) ? $__USER_INFO['type'] : (int)$_POST['type'];
        if(!empty($_POST['password'])){
                
                import::getInt('ucenter');
                $ret = ucenterInt::getInstance()->edit($__USER_INFO['username'],$password2,$password);
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
                }  else {
                    if(md5($password2) != $__USER_INFO['password']) errorAlert ('旧密码不正确#001');
                }
               
        }
        
        if (false === usersMdl::getInstance()->updateUsers($uid, $info))
            errorAlert('操作失败');
        if(!empty($_POST['password'])){ //滞后处理
                import::getMdl('outtoin'); //忽略空结果集 只要更新即可
                outtoinMdl::getInstance()->updateOuttoinByUid($uid,array('password'=>  authcode($_POST['password'])));
        }       
        
        try{
           import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],100,100);
                    $ex['uid'] = $uid;
                    $ex['face_pic'] = $face_pic['web_file_name'];
                    if(!empty($userEx['face_pic'])){
                        if(file_exists(BASE_PATH.$userEx['face_pic'])) unlink (BASE_PATH.$userEx['face_pic']);
                    }
                    usersMdl::getInstance()->replaceUsersEx($ex);
                }    
            }
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        echoJs("alert('操作成功');parent.location='" . mkUrl::linkTo('user','info') . "'");
        die;
    }
    
    require TEMPLATE_PATH.'user/info.html';
    die;
}
if($_GET['act'] === 'authenticate'){
    $userEx = usersMdl::getInstance()->getUsersEx($uid);
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        try{
           import::getLib('uploadimg');
            if(!empty($_FILES['certificate']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('certificate');
                if(!empty($face_pic['web_file_name'])) {
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],600,600);
                    $ex['uid'] = $uid;
                    $ex['certificate'] = $face_pic['web_file_name'];
                    if(!empty($userEx['certificate'])){
                        if(file_exists(BASE_PATH.$userEx['certificate'])) unlink (BASE_PATH.$userEx['certificate']);
                    }
                    usersMdl::getInstance()->replaceUsersEx($ex);
                }    
            }
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
         echoJs("alert('操作成功');parent.location='" . mkUrl::linkTo('user','authenticate') . "'");
         die;
     }
     require TEMPLATE_PATH.'user/authenticate.html';
    die;
}

if($_GET['act'] === 'mytenders'){
    import::getMdl('bidding');
    $url = array();
    $where = array(
        'uid' => $uid
    );
   
    $totalnum = biddingMdl::getInstance()->getBiddingCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`name`','`building_name`','`addr`','`mobile`','`is_show`');     
    $datas = biddingMdl::getInstance()->getBiddingList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','mytenders',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/mytenders.html';
    die;
}

if($_GET['act'] === 'template'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('company');
    $data = companyMdl::getInstance()->getCompany($uid);    
    if(empty($data)) ucenterNoAccess ();
    $templateId = empty($data['template_id']) ?  0 : (int)$data['template_id'];
    $tempcfg = require BASE_PATH .'themes/company/style.cfg.php';
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $template_id = empty($_POST['template_id']) ? 0 : (int)$_POST['template_id'];
        if(!isset($tempcfg[$template_id]))errorAlert ('没有该模版');
        $info['template_id'] = $template_id;
        if(false === companyMdl::getInstance()->updateCompany($uid,$info)) errorAlert ('更换模版失败');
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','template')."'");
        die;
    }
    require TEMPLATE_PATH.'user/template.html';
    die;
}


if($_GET['act'] === 'company'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('companyArea');
    import::getMdl('companyProject');
    import::getMdl('companyIndustry');
    import::getMdl('companyKeywordMaps');
    import::getInt('category');
    import::getLib('pscws5');
    import::getMdl('keywords');
    import::getMdl('company');
    $data = companyMdl::getInstance()->getCompany($uid);    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(empty($data)) $info['uid'] = $uid;
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('公司所在地区不能为空');
        $info['type'] = (int)$__USER_INFO['type'];
        
        $info['company_name'] = empty($_POST['company_name']) ? '': trim(htmlspecialchars($_POST['company_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['company_name'])) errorAlert('公司名称不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(!empty($data)) $info['logo'] = $data['logo'];
        if(!empty($data)) $info['banner'] = $data['banner'];
        $info['founding_year'] =empty($_POST['founding_year']) ? 0: (int)$_POST['founding_year'];

        $info['scale_id'] =empty($_POST['scale_id']) ? 0: (int)$_POST['scale_id'];
        $info['output_id'] =empty($_POST['output_id']) ? 0: (int)$_POST['output_id'];
        $info['free_room'] =empty($_POST['free_room']) ? 2 : (int)$_POST['free_room'];
        
      
        
        $info['introduce'] = empty($_POST['introduce']) ? '': trim(htmlspecialchars($_POST['introduce'],ENT_QUOTES,'UTF-8'));
        $areaids = empty($_POST['areas']) ? errorAlert('请选择服务区域') : $_POST['areas'];
        if($__USER_TYPE['company'] === (int)$__USER_INFO['type']) $projectids =     empty($_POST['projects']) ? errorAlert('请选择服务项目') : $_POST['projects'];
        if($__USER_TYPE['material'] === (int)$__USER_INFO['type'])  $industryids =     empty($_POST['industrys']) ? errorAlert('请选择行业') : $_POST['industrys'];
        $delpics = array();
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['logo']['tmp_name'])){
                $logo = uploadImg::getInstance()->upload('logo');
                if(!empty($logo['web_file_name'])) {
                    $info['logo'] = $logo['web_file_name'];
                    uploadImg::getInstance()->resizeImage($logo['store_file_name'],$logo['store_file_name'],126,63);
                    if(!empty($data['logo'])) $delpics[] = $data['logo'];
                }
            }else{
                if(empty($data['logo'])) errorAlert ('请上传LOGO');
            }
            if(!empty($_FILES['banner']['tmp_name'])){
                $banner = uploadImg::getInstance()->upload('banner');
                if(!empty($banner['web_file_name'])) {
                    $info['banner'] = $banner['web_file_name'];
                    if(!empty($data['banner'])) $delpics[] = $data['banner'];
                }
            }
           
            
        }  catch (Exception $e){
            
            errorAlert($e->getMessage());
           
        }
        if(!empty($data)){
            if(false === companyMdl::getInstance()->updateCompany($uid,$info)) errorAlert ('操作失败');
        }else{
          
            if(!companyMdl::getInstance()->addCompany($info)) errorAlert ('操作失败');
            
        }
        
        if(!empty($data)) {
            foreach($delpics as $v){
                if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
            }
        }
        if(!empty($data)) companyAreaMdl::getInstance()->delcompanyAreaById($uid);
        foreach($areaids as $v){
            companyAreaMdl::getInstance()->addcompanyArea(array('uid'=>$uid,'area_id'=> (int)$v));
        }
        if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
            if(!empty($data)) companyProjectMdl::getInstance()->delcompanyProjectById($uid);
            foreach($projectids as $v){
                companyProjectMdl::getInstance()->addcompanyProject(array('uid'=>$uid,'project_id'=> (int)$v));
            }
        }
        if($__USER_TYPE['material'] === (int)$__USER_INFO['type']){
            if(!empty($data)) companyIndustryMdl::getInstance()->delcompanyIndustryById($uid);
            foreach($industryids as $v){
                companyIndustryMdl::getInstance()->addcompanyIndustry(array('uid'=>$uid,'industry_id'=> (int)$v));
            }
        }
        
        
        //生成关键字MAP 
        if(!empty($data)) companyKeywordMapsMdl::getInstance()->delCompanyKeywordMapsByuid($uid);
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['company_name']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['uid'] = $uid;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            companyKeywordMapsMdl::getInstance()->addCompanyKeywordMaps($mapinfo);
        }
     
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','company')."'");
        die;
    } 
    

    if($__USER_TYPE['company'] === (int)$__USER_INFO['type']) $projects = category::getInstance()->getChildCol($__CATEGORY_TYPE['company'],$__COMPANY_CATEGORY_ROOT['project']);
    if($__USER_TYPE['material'] === (int)$__USER_INFO['type']) $industrys = category::getInstance()->getChildCol($__CATEGORY_TYPE['company'],$__COMPANY_CATEGORY_ROOT['industry']);
    $scales  = category::getInstance()->getChildCol($__CATEGORY_TYPE['company'],$__COMPANY_CATEGORY_ROOT['scale']);
    $outputs  = category::getInstance()->getChildCol($__CATEGORY_TYPE['company'],$__COMPANY_CATEGORY_ROOT['output']);
    $areas = areaInt::getInstance()->getAreas();
    if(!empty($data)){
        if($__USER_TYPE['company'] === (int)$__USER_INFO['type']) $projectIds = companyProjectMdl::getInstance()->getcompanyProjectcCol($uid);
         if($__USER_TYPE['material'] === (int)$__USER_INFO['type']) $industryIds =  companyIndustryMdl::getInstance()->getcompanyIndustryCol($uid);
         $areaIds = companyAreaMdl::getInstance()->getcompanyAreacCol($uid);
         require TEMPLATE_PATH.'user/company_edit.html';
    }else{
        require TEMPLATE_PATH.'user/company_add.html';
    }
    die;
}


if($_GET['act'] === 'addr'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    //如果是公司 操作公司项之前先判断资料填写了没有
    $defaultAddrId = 0;
    
    import::getMdl('company');
    $company = companyMdl::getInstance()->getCompany($uid);   
    if(empty($company)) errorAlert ('您还为填充公司资料还不能操作此项！');
    if(!empty($company['addr_id'])) $defaultAddrId = (int)$company['addr_id'];

    
    import::getMdl('companyAddrs');
    $id = empty($_GET['id']) ? 0 : (int)$_GET['id'];
    $data = companyAddrsMdl::getInstance()->getCompanyAddrs($id);
    if(!empty($data) && (int)$data['uid'] !== $uid) ucenterNoAccess();
    if($id != 0 && empty($data)) ucenterNoAccess();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('面市或公司名称不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('电话不能为空');
        $info['fax'] = empty($_POST['fax']) ? '': trim(htmlspecialchars($_POST['fax'],ENT_QUOTES,'UTF-8'));
        if(empty($info['fax'])) errorAlert('传真不能为空');
        $info['mobile'] =empty($_POST['mobile']) ? '': $_POST['mobile'];
        if(empty($info['mobile'])) errorAlert('手机不能为空');
        if(!isMobile($info['mobile'])) errorAlert ('手机号码格式不正确');
        $info['contact'] = empty($_POST['contact']) ? '': trim(htmlspecialchars($_POST['contact'],ENT_QUOTES,'UTF-8'));
        if(empty($info['contact'])) errorAlert('联系人不能为空');
        $info['addr'] = empty($_POST['addr']) ? '': trim(htmlspecialchars($_POST['addr'],ENT_QUOTES,'UTF-8'));
        if(empty($info['addr'])) errorAlert('详细地址不能为空');
        $info['pic'] = '';
        if(!empty($data)){
            $info['pic'] = $data['pic'];
            $delpics = array();
        }
        
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['pic']['tmp_name'])){ 
                $pic = uploadImg::getInstance()->upload('pic');
                if(!empty($pic['web_file_name'])) {
                    $info['pic'] = $pic['web_file_name'];
                    if(!empty($data)) $delpics[] = $data['pic'];
                }
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(empty($data)){
            if(!companyAddrsMdl::getInstance()->addCompanyAddrs($info)) errorAlert ('操作失败');
        }else{
            if(false === companyAddrsMdl::getInstance()->updateCompanyAddrs($id,$info)) errorAlert ('操作失败');
            foreach($delpics as $v){
                if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
            }
        }
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','addr')."'");
        die;
    } 
    $datas = companyAddrsMdl::getInstance()->getAllCompanyAddrs($uid);
   
    //材料商的判断 在后面补上
    
    require TEMPLATE_PATH.'user/company_addrs.html';
    die;
}

if($_GET['act'] === 'delAddr'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] )        errorAlert('不可操作');
    import::getMdl('companyAddrs');
    $id = empty($_GET['id']) ? 0 : (int)$_GET['id'];
    $data = companyAddrsMdl::getInstance()->getCompanyAddrs($id);
    if(empty($data))   errorAlert('不可操作');
    if((int)$data['uid'] !== $uid)   errorAlert('不可操作');
   
    if(false !== companyAddrsMdl::getInstance()->delCompanyAddrs($id)) {
        if(!empty($data['pic'])){
            if(file_exists(BASE_PATH.$data['pic'])) unlink(BASE_PATH.$data['pic']);
        }
       
        dieJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','addr').'"');
    }
    errorAlert('操作失败');
    die;
}

if($_GET['act'] === 'defaultAddr'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] )  errorAlert('不可操作');
    import::getMdl('companyAddrs');
    $id = empty($_GET['id']) ? errorAlert('不可操作') : (int)$_GET['id'];
    $data = companyAddrsMdl::getInstance()->getCompanyAddrs($id);
    if(empty($data))   errorAlert('不可操作');
    if((int)$data['uid'] !== $uid)   errorAlert('不可操作');
    
   
    import::getMdl('company');
    $company = companyMdl::getInstance()->getCompany($uid);   
    if(empty($company)) errorAlert ('您还为填充公司资料还不能操作此项！');
    $info['addr_id'] = $id;
    if(false ===  companyMdl::getInstance()->updateCompany($uid,$info)) errorAlert ('操作失败');
    dieJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','addr').'"');
   
    die;
}


if($_GET['act'] === 'qq'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] )  errorAlert('不可操作');
    import::getMdl('companyQqs');
    $defaultAddrId = 0;
    if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
        import::getMdl('company');
        $company = companyMdl::getInstance()->getCompany($uid);   
        if(empty($company)) errorAlert ('您还为填充公司资料还不能操作此项！');
        if(!empty($company['qq_id'])) $defaultAddrId = (int)$company['qq_id'];
    }
    $id = empty ($_GET['id']) ? 0 : (int)$_GET['id'];    
    $data = companyQqsMdl::getInstance()->getCompanyQqs($id);    
    if(!empty($data) && (int)$data['uid'] !== $uid) ucenterNoAccess();
    if($id != 0 && empty($data)) ucenterNoAccess();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('客服名称不能为空');
        $info['qq'] = empty($_POST['qq']) ? '': trim(htmlspecialchars($_POST['qq'],ENT_QUOTES,'UTF-8'));
        if(empty($info['qq'])) errorAlert('QQ号码不能为空');
        if(!isQQ($info['qq'])) errorAlert ('QQ号码格式不正确');
        if(empty($data)){
            if(!companyQqsMdl::getInstance()->addCompanyQqs($info)) errorAlert ('操作失败');
        }else{
            if(false === companyQqsMdl::getInstance()->updateCompanyQqs($id,$info)) errorAlert ('操作失败');
        }
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','qq')."'");
        die;
    } 
    $datas = companyQqsMdl::getInstance()->getAllCompanyQqs($uid);
    require TEMPLATE_PATH.'user/company_qq.html';
    die;
}


if($_GET['act'] === 'defaultQQ'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] )  errorAlert('不可操作');
    import::getMdl('companyQqs');
    $id = empty($_GET['id']) ? errorAlert('不可操作') : (int)$_GET['id'];
    $data = companyQqsMdl::getInstance()->getCompanyQqs($id);    
    if(empty($data))   errorAlert('不可操作');
    if((int)$data['uid'] !== $uid)   errorAlert('不可操作');
    
    if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
        import::getMdl('company');
        $company = companyMdl::getInstance()->getCompany($uid);   
        if(empty($company)) errorAlert ('您还为填充公司资料还不能操作此项！');
        $info['qq_id'] = $id;
        if(!companyMdl::getInstance()->updateCompany($uid,$info)) errorAlert ('操作失败');
        dieJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','qq').'"');
    }
    die;
}

if($_GET['act'] === 'delQQ'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] )  errorAlert('不可操作');
    import::getMdl('companyQqs');
    $id = empty($_GET['id']) ? errorAlert('不可操作') : (int)$_GET['id'];
    $data = companyQqsMdl::getInstance()->getCompanyQqs($id);    
    if(empty($data))   errorAlert('不可操作');
    if((int)$data['uid'] !== $uid)   errorAlert('不可操作');
    if(false !== companyQqsMdl::getInstance()->delCompanyQqs($id)) {
       dieJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','qq').'"');
    }
    errorAlert('操作失败');
    die;
}


if($_GET['act'] === 'mydianping'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('companyDianping');
    $where  = array(
        'company_id' => $uid
    );
    $totalnum = companyDianpingMdl::getInstance()->getCompanyDianpingCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');  
    $col = array('a.*','c.username');     
    $datas = companyDianpingMdl::getInstance()->getCompanyDianpingList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('users','mydianping',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/mydianping.html';
    die;
} 


if($_GET['act'] === 'mydianpingReply'){
    import::getMdl('companyDianping');
    $id = empty ($_GET['id']) ? ucenterNoAccess(): (int)$_GET['id'];    
    $data = companyDianpingMdl::getInstance()->getCompanyDianping($id);    
    if(empty($data)) ucenterNoAccess();
    if($data['company_id'] != $uid) errorAlert ('违规操作');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['revert'] = empty($_POST['revert']) ? '': trim(htmlspecialchars($_POST['revert'],ENT_QUOTES,'UTF-8'));
        if(false === companyDianpingMdl::getInstance()->updateCompanyDianping($id,$info)) errorAlert ('操作失败');
        echoJs("alert('操作成功');parent.location='". mkUrl::linkTo('user','mydianping')."'");
        die;
    } 
    require TEMPLATE_PATH.'user/mydianpingReply.html';
    die;
}



if($_GET['act'] === 'case'){
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('case',$uid)) ucenterNoAccess();
    //ucenterNoAccess();
    import::getMdl('case');
    import::getInt('category');
    $where = array(
        'uid' => $uid
    );

    $totalnum = caseMdl::getInstance()->getCaseCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('case_id'=>'DESC');
    
    $col = array('`case_id`','`title`','`type`','`face_pic`','`real_price`','`real_space`','`pv_num`','`is_show`','`create_time`');
    
    $datas = caseMdl::getInstance()->getCaseList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','case',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);

    require TEMPLATE_PATH.'user/caseList.html';
    die;
}

if($_GET['act'] === 'caseEdit'){
    import::getInt('authority');
    import::getMdl('caseMap');
    if(!authorityInt::getInstance()->isAuthority('case',$uid)) ucenterNoAccess();
    import::getMdl('case');
    import::getInt('category');
    $case_id = empty ($_GET['id']) ? ucenterNoAccess(): (int)$_GET['id'];    
    $data = caseMdl::getInstance()->getCase($case_id);    
    if(empty($data)) ucenterNoAccess();
    if((int)$data['uid']!== $uid) ucenterNoAccess (mkUrl::linkTo('user','case'));
    $data['detail_pics'] = json_decode($data['detail_pics'],true);
    $data['detail_pics'] = empty($data['detail_pics']) ?  array() : $data['detail_pics'];
    
    import::getMdl('designer');
    if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
        $designerPair = designerMdl::getInstance()->getAllDesignerPairByUid($uid);
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){

         
        $info['title'] = empty($_POST['title']) ? errorAlert('案例标题不能为空') : trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));

        $cate_ids = empty($_POST['cate_id']) ? errorAlert('请选择类型') : $_POST['cate_id'];
        if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
            $info['designer_id'] =empty($_POST['designer_id']) ? errorAlert('请选择设计师') : (int)$_POST['designer_id'];
            if(!isset($designerPair[$info['designer_id']])) errorAlert ('请选择自己的设计师');
        }else{
            $info['designer_id'] = designerMdl::getInstance()->getDesignerIdByUid($uid); //可以忽略
        }
        
        $info['type'] =empty($_POST['type']) ? errorAlert('请选择类型') : (int)$_POST['type'];
        $info['real_price'] =empty($_POST['real_price']) ? 0 : (int)($_POST['real_price']*100);
        $info['real_space'] =empty($_POST['real_space']) ? 0 : (int)($_POST['real_space']*100);
        $info['keywords'] = empty($_POST['keywords']) ? '' : trim(htmlspecialchars($_POST['keywords'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '' : trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));

        $info['face_pic'] = $data['face_pic'];  
        $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
        $delpics = array();
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];
                }    
            }
           
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $oldpics = empty($_POST['oldpics']) ? array() : $_POST['oldpics'];
        foreach( $data['detail_pics'] as $k=>$v){
            if(!in_array($v,$oldpics)){
               //$delpics[] = $v; 不能删除存在安全隐患
               unset($data['detail_pics'][$k]);
            }
        }
        $info['detail_pics'] = json_encode(array_merge($data['detail_pics'] , $detailPics));
        if(false === caseMdl::getInstance()->updateCase($case_id,$info)) errorAlert ('更新失败');
        $cateInfo = array();
        foreach($cate_ids as $v){
            $cateInfo[] = array('case_id'=>$case_id,'cate_id'=>(int)$v);
        }
        if(!empty($cateInfo)) {
            caseMapMdl::getInstance()->delCaseMaps($case_id);
            caseMapMdl::getInstance()->addCaseMaps($cateInfo);
        }
        
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','caseEdit',array('id'=>$case_id))."'");
        die;
    }
    $cateIds = caseMapMdl::getInstance()->getCaseMapsByCaseId($case_id);
    require TEMPLATE_PATH.'user/caseEdit.html';
    die;
}



if($_GET['act'] === 'caseAdd'){
    import::getInt('authority');
    import::getMdl('caseMap');
    if(!authorityInt::getInstance()->isAuthority('case',$uid)) ucenterNoAccess();
    import::getMdl('case');
    import::getInt('category');
    import::getMdl('designer');
    if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
        $designerPair = designerMdl::getInstance()->getAllDesignerPairByUid($uid);
    }
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid']  = $uid;
        $info['title'] = empty($_POST['title']) ? errorAlert('案例标题不能为空') : trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));

        $cate_ids = empty($_POST['cate_id']) ? errorAlert('请选择类型') : $_POST['cate_id'];
         
        if($__USER_TYPE['company'] === (int)$__USER_INFO['type']){
            $info['designer_id'] =empty($_POST['designer_id']) ? errorAlert('请选择设计师') : (int)$_POST['designer_id'];
            if(!isset($designerPair[$info['designer_id']])) errorAlert ('请选择自己的设计师');
        }else{
            $info['designer_id'] = designerMdl::getInstance()->getDesignerIdByUid($uid); //可以忽略
        }
        
        $info['type'] =empty($_POST['type']) ? errorAlert('请选择类型') : (int)$_POST['type'];
        $info['real_price'] =empty($_POST['real_price']) ? 0 : (int)($_POST['real_price']*100);
        $info['real_space'] =empty($_POST['real_space']) ? 0 : (int)($_POST['real_space']*100);
        $info['keywords'] = empty($_POST['keywords']) ? '' : trim(htmlspecialchars($_POST['keywords'],ENT_QUOTES,'UTF-8'));
        $info['description'] = empty($_POST['description']) ? '' : trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));

        $info['face_pic'] = '';  
         $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $info['detail_pics'] = json_encode($detailPics);
        $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
        $info['is_show'] = (int)authorityInt::getInstance()->isShow();
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        $case_id = caseMdl::getInstance()->addCase($info);
        if(!$case_id) errorAlert ('添加失败');
        $cateInfo = array();
        foreach($cate_ids as $v){
            $cateInfo[] = array('case_id'=>$case_id,'cate_id'=>(int)$v);
        }
        if(!empty($cateInfo)) {
            caseMapMdl::getInstance()->addCaseMaps($cateInfo);
        }
        import::getInt('integral');
        integralInt::getInstance()->obtain($__INTEGRAL_GAIN['case']);
        echoJs("alert('添加成功');parent.location='".mkUrl::linkTo('user','case')."'");
        die;
    } 
    require TEMPLATE_PATH.'user/caseAdd.html';
    die;
}

if($_GET['act'] === 'pics'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('companyPics');
    $where = array('uid'=>$uid);
    $totalnum = companyPicsMdl::getInstance()->getCompanyPicsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`type`','`title`','`pic`');     
    $datas = companyPicsMdl::getInstance()->getCompanyPicsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','pics',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/pics.html';
    die;
}


if($_GET['act'] === 'picsAdd'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('companyPics');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['pic'] = '';

        try{
            import::getLib('uploadimg');
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) $info['pic'] = $pic['web_file_name'];
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!companyPicsMdl::getInstance()->addCompanyPics($info)) errorAlert ('操作失败');
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','pics')."'");
        die;
    } 

    require TEMPLATE_PATH.'user/picsAdd.html';
    die;
}

if($_GET['act'] === 'picsEdit'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('companyPics');
    $id = empty ($_GET['id']) ? ucenterNoAccess(): (int)$_GET['id'];    
    $data = companyPicsMdl::getInstance()->getCompanyPics($id);    
    if(empty($data)) ucenterNoAccess();
    if((int)$data['uid'] !== $uid)        ucenterNoAccess();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['type'] =empty($_POST['type']) ? 0: (int)$_POST['type'];
        if(empty($info['type'])) errorAlert('类型不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['pic'] = $data['pic'];
        $delpics = array();
        try{
            import::getLib('uploadimg');
            $pic = uploadImg::getInstance()->upload('pic');
            if(!empty($pic['web_file_name'])) {
                    $info['pic'] = $pic['web_file_name'];
                    $delpics[] = $data['pic'];    
                }
            
        }  catch (Exception $e){
            if(empty($data['pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === companyPicsMdl::getInstance()->updateCompanyPics($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
     
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','picsEdit',array('id'=>$id))."'");
        die;
    } 

    require TEMPLATE_PATH.'user/picsEdit.html';
    die;
        
}


if($_GET['act'] === 'picsDel'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('companyPics');
    $id = empty ($_GET['id']) ? ucenterNoAccess() : (int)$_GET['id'];    
    $data = companyPicsMdl::getInstance()->getCompanyPics($id);    
    if(empty($data)) ucenterNoAccess();
    if((int)$data['uid'] !== $uid)        ucenterNoAccess();
    if(false !== companyPicsMdl::getInstance()->delCompanyPics($id)) {
        if(!empty($data['pic'])){
            if(file_exists(BASE_PATH.$data['pic'])) unlink(BASE_PATH.$data['pic']);
        }
       
        dieJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','pics').'"');
    }
    errorAlert('操作失败');
    die;
}


if($_GET['act'] === 'designer'){
    if($__USER_TYPE['designer'] !== (int)$__USER_INFO['type']) ucenterNoAccess ();
    import::getMdl('designer');

    $data = designerMdl::getInstance()->getDesignerByUid($uid);    
    
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
   
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名字不能为空');
        $info['position'] = empty($_POST['position']) ? '': trim(htmlspecialchars($_POST['position'],ENT_QUOTES,'UTF-8'));
        if(empty($info['position'])) errorAlert('职位不能为空');
        $info['school'] = empty($_POST['school']) ? '': trim(htmlspecialchars($_POST['school'],ENT_QUOTES,'UTF-8'));
        if(empty($info['school'])) errorAlert('毕业院校不能为空');
        $info['from_time'] =empty($_POST['from_time']) ? 0: (int)$_POST['from_time'];
        if(empty($info['from_time'])) errorAlert('工作经验不能为空');
        $info['style'] = empty($_POST['style']) ? '': trim(htmlspecialchars($_POST['style'],ENT_QUOTES,'UTF-8'));
        if(empty($info['style'])) errorAlert('设计风格不能为空');
        $info['about'] = empty($_POST['about']) ? '': trim(htmlspecialchars($_POST['about'],ENT_QUOTES,'UTF-8'));
        if(empty($info['about'])) errorAlert('自我介绍不能为空');
        $info['qq'] = empty($_POST['qq']) ? '': trim(htmlspecialchars($_POST['qq'],ENT_QUOTES,'UTF-8'));
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(!empty($data)){
            $info['face_pic'] = $data['face_pic'];
            $delpics = array();
        }
        try{
            import::getLib('uploadimg');
             if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
                if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
                if(!empty($data)) $delpics[] = $data['face_pic'];
             }
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        if(empty($data)){
            if(!designerMdl::getInstance()->addDesigner($info)) errorAlert ('操作失败');
        }else{
            if(false === designerMdl::getInstance()->updateDesigner($data['id'],$info)) errorAlert ('操作失败');
            foreach($delpics as $v){
                if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
            }
        }
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','designer')."'");
        die;
    } 
     $areas = areaInt::getInstance()->getAreas();
    if(empty($data)){
       require TEMPLATE_PATH.'user/designerAdd.html'; 
    }else{
      
       require TEMPLATE_PATH.'user/designerEdit.html';  
    }
    die;
}


if($_GET['act'] === 'designerList'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('designer');
    $url = array(); 
    $where = array('uid'=>$uid);
    $totalnum = designerMdl::getInstance()->getDesignerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','`face_pic`','`area_id`','`name`','`position`','`school`','`from_time`','`style`');     
    $datas = designerMdl::getInstance()->getDesignerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','designerList',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/com_designerList.html';
    die;
}

if($_GET['act'] === 'designerAdd'){
    import::getMdl('designer');
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名字不能为空');
        $info['position'] = empty($_POST['position']) ? '': trim(htmlspecialchars($_POST['position'],ENT_QUOTES,'UTF-8'));
        if(empty($info['position'])) errorAlert('职位不能为空');
        $info['school'] = empty($_POST['school']) ? '': trim(htmlspecialchars($_POST['school'],ENT_QUOTES,'UTF-8'));
        if(empty($info['school'])) errorAlert('毕业院校不能为空');
        $info['from_time'] =empty($_POST['from_time']) ? 0: (int)$_POST['from_time'];
        if(empty($info['from_time'])) errorAlert('工作经验不能为空');
        $info['style'] = empty($_POST['style']) ? '': trim(htmlspecialchars($_POST['style'],ENT_QUOTES,'UTF-8'));
        if(empty($info['style'])) errorAlert('设计风格不能为空');
        $info['about'] = empty($_POST['about']) ? '': trim(htmlspecialchars($_POST['about'],ENT_QUOTES,'UTF-8'));
        if(empty($info['about'])) errorAlert('自我介绍不能为空');
        $info['qq'] = empty($_POST['qq']) ? '': trim(htmlspecialchars($_POST['qq'],ENT_QUOTES,'UTF-8'));
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
         try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
           uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        if(!designerMdl::getInstance()->addDesigner($info)) errorAlert ('操作失败');
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','designerList')."'");
        die;
    } 
    $areas = areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'user/com_designerAdd.html';
    die;
}


if($_GET['act'] === 'designerEdit'){
    import::getMdl('designer');
    $id = empty ($_GET['id']) ? ucenterNoAccess() : (int)$_GET['id'];    
    $data = designerMdl::getInstance()->getDesigner($id);    
    if(empty($data)) ucenterNoAccess();
    if((int)$data['uid']!== $uid) ucenterNoAccess();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
      
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('名字不能为空');
        $info['position'] = empty($_POST['position']) ? '': trim(htmlspecialchars($_POST['position'],ENT_QUOTES,'UTF-8'));
        if(empty($info['position'])) errorAlert('职位不能为空');
        $info['school'] = empty($_POST['school']) ? '': trim(htmlspecialchars($_POST['school'],ENT_QUOTES,'UTF-8'));
        if(empty($info['school'])) errorAlert('毕业院校不能为空');
        $info['from_time'] =empty($_POST['from_time']) ? 0: (int)$_POST['from_time'];
        if(empty($info['from_time'])) errorAlert('工作经验不能为空');
        $info['style'] = empty($_POST['style']) ? '': trim(htmlspecialchars($_POST['style'],ENT_QUOTES,'UTF-8'));
        if(empty($info['style'])) errorAlert('设计风格不能为空');
        $info['about'] = empty($_POST['about']) ? '': trim(htmlspecialchars($_POST['about'],ENT_QUOTES,'UTF-8'));
        if(empty($info['about'])) errorAlert('自我介绍不能为空');
        $info['qq'] = empty($_POST['qq']) ? '': trim(htmlspecialchars($_POST['qq'],ENT_QUOTES,'UTF-8'));
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        $info['face_pic'] = $data['face_pic'];
        $delpics = array();
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],250,250);
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];
                }    
            }
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        if(false === designerMdl::getInstance()->updateDesigner($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','designerEdit',array('id'=>$id))."'");
        die;
    } 

    $areas = areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'user/com_designerEdit.html';
    die;
}    

if($_GET['act'] === 'designerDel'){
    $id = empty ($_GET['id']) ? errorAlert('参数错误') : (int)$_GET['id'];    
    $data = designerMdl::getInstance()->getDesigner($id);    
    if(empty($data)) errorAlert ('参数出错');
    if((int)$data['uid'] !== $uid) errorAlert ('不可操作');
    if(false !== designerMdl::getInstance()->delDesigner($id)) {
       if(!empty($data['face_pic'])){
            if(file_exists(BASE_PATH.$data['face_pic'])) unlink(BASE_PATH.$data['face_pic']);
        }
        dieJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','designerList').'"');
    }
    errorAlert('操作失败');
    die;
}


if($_GET['act'] === 'preferential'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('preferential');
    $url = array(); 
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('preferential',$uid))        ucenterNoAccess ();
    
    $where  = array('uid'=>$uid);
    $totalnum = preferentialMdl::getInstance()->getPreferentialCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','`title`','`face_pic`');     
    $datas = preferentialMdl::getInstance()->getPreferentialList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','preferential',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/preferential.html';
    die;
}

if($_GET['act'] === 'preferentialAdd'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('preferential',$uid))        ucenterNoAccess ();
    import::getMdl('preferential');
    import::getMdl('preferentialKeywordMaps');
    import::getLib('pscws5');
    import::getMdl('keywords');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('优惠标题不能为空');

        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['face_pic'] = '';
        $info['content'] = empty($_POST['content']) ? '': getValue($_POST['content']);
        $info['create_time'] = NOWTIME;
        $info['ip'] = getIp();
        $info['is_show'] = (int)authorityInt::getInstance()->isShow();
        if(empty($info['content'])) errorAlert('优惠内容不能为空');

        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['face_pic']['tmp_name'])){ 
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
           } 
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $id = preferentialMdl::getInstance()->addPreferential($info);
        if(!$id) errorAlert ('操作失败');
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['preferential_id'] = $id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            preferentialKeywordMapsMdl::getInstance()->addPreferentialKeywordMaps($mapinfo);
        }
        import::getInt('integral');
        integralInt::getInstance()->obtain($__INTEGRAL_GAIN['preferential']);
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','preferential')."'");
        die;
    } 
    $areas = areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'user/preferentialAdd.html';
    die;
}

if($_GET['act'] === 'preferentialEdit'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('preferential',$uid))        ucenterNoAccess ();
    import::getMdl('preferential');
    import::getMdl('preferentialKeywordMaps');
    import::getLib('pscws5');
    import::getMdl('keywords');
    $id = empty ($_GET['id']) ?ucenterNoAccess(): (int)$_GET['id'];    
    $data = preferentialMdl::getInstance()->getPreferential($id);    
    if(empty($data)) ucenterNoAccess();
    if((int)$data['uid']!== $uid) ucenterNoAccess ();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('优惠标题不能为空');
   
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('地区不能为空');
        $info['face_pic'] = $data['face_pic'];
        $info['content'] = empty($_POST['content']) ? '': getValue($_POST['content']);
        if(empty($info['content'])) errorAlert('优惠内容不能为空');
        
        $delpics = array();
        try{
            import::getLib('uploadimg');
           if(!empty($_FILES['face_pic']['tmp_name'])){ 
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) {
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];    
                }
           } 
            
        }  catch (Exception $e){
            if(empty($data['face_pic'])){
                errorAlert($e->getMessage());
            }
        }
        
        if(false === preferentialMdl::getInstance()->updatePreferential($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        
        preferentialKeywordMapsMdl::getInstance()->delPreferentialKeywordMapsByPreferentialId($id);
        
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['preferential_id'] = $id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            preferentialKeywordMapsMdl::getInstance()->addPreferentialKeywordMaps($mapinfo);
        }
     
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','preferentialEdit',array('id'=>$id))."'");
        die;
    } 
   
    $areas = areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'user/preferentialEdit.html';
    die;
}


if($_GET['act'] === 'diary'){
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('diary',$uid))        ucenterNoAccess();
    import::getMdl('diary');
    import::getInt('category');
    
    $where  = array('uid'=>$uid);
    
    $totalnum = diaryMdl::getInstance()->getDiaryCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC'); 
    $col = array('`id`','`title`','`cate_id`');     
    $datas = diaryMdl::getInstance()->getDiaryList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','diary',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    
    require TEMPLATE_PATH.'user/diary.html';
    die;
}

if($_GET['act'] === 'diaryAdd'){
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('diary',$uid))        ucenterNoAccess();
    import::getMdl('diary');
    import::getInt('category');
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['cate_id'] =empty($_POST['cate_id']) ? 0: (int)$_POST['cate_id'];
        if(empty($info['cate_id'])) errorAlert('类别不能为空');
        $info['uid'] = $uid;
        $info['contents'] = empty($_POST['contents']) ? '': getValue($_POST['contents']);
        if(empty($info['contents'])) errorAlert('日记内容不能为空');
        $info['create_time'] = NOWTIME;
        $info['is_show'] = (int)authorityInt::getInstance()->isShow();
        if(!diaryMdl::getInstance()->addDiary($info)) errorAlert ('操作失败');
        import::getInt('integral');
        integralInt::getInstance()->obtain($__INTEGRAL_GAIN['diary']);
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','diaryAdd')."'");
        die;
    } 
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['lc']);
    require TEMPLATE_PATH.'user/diaryAdd.html';
    die;
}

if($_GET['act'] === 'diaryEdit'){
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('diary',$uid))        ucenterNoAccess();
    import::getMdl('diary');
    import::getInt('category');
    $id = empty ($_GET['id']) ? ucenterNoAccess(): (int)$_GET['id'];    
    $data = diaryMdl::getInstance()->getDiary($id);    
    if(empty($data)) ucenterNoAccess();
    if((int)$data['uid'] !== $uid) ucenterNoAccess ();
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['cate_id'] =empty($_POST['cate_id']) ? 0: (int)$_POST['cate_id'];
        if(empty($info['cate_id'])) errorAlert('类别不能为空');
        $info['contents'] = empty($_POST['contents']) ? '': getValue($_POST['contents']);
        if(empty($info['contents'])) errorAlert('日记内容不能为空');
        
        if(false === diaryMdl::getInstance()->updateDiary($id,$info)) errorAlert ('操作失败');

        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','diaryEdit',array('id'=>$id))."'");
        die;
    } 
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['lc']);
    require TEMPLATE_PATH.'user/diaryEdit.html';
    die;
}

if($_GET['act'] === 'ask'){
    import::getMdl('ask');
    $where = array(
        'uid' => $uid
    );
    $totalnum = askMdl::getInstance()->getAskCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`ip`','`title`','`integral`');     
    $datas = askMdl::getInstance()->getAskList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','ask',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/ask.html';
    die;
}

if($_GET['act'] === 'answer'){
    import::getMdl('askAnswer');
    $where['uid'] = $uid;
    $totalnum = askAnswerMdl::getInstance()->getAskAnswerCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');   
    $col = array('a.`id`','b.`username`','a.`uid`','a.`ask_id`','a.`content`','a.`create_time`','a.`ip`');     
    $datas = askAnswerMdl::getInstance()->getAskAnswerList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links =createPage(mkUrl::linkTo('user','answer',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/answer.html';
    die;
}


if($_GET['act'] === 'products'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('products',$uid)) ucenterNoAccess();
    import::getMdl('products');
    import::getInt('category');

    $where  = array('company_id'=>$uid);
    $totalnum = productsMdl::getInstance()->getProductsCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    
    $col = array('`id`','`product_name`','`category_id`','`brand_id`','`company_id`','`face_pic`','`market_price`','`mall_price`');     
    $datas = productsMdl::getInstance()->getProductsList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','products',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    import::getMdl('brand');
    foreach ($datas as $k => $v) {
        $datas[$k]['category_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['products'], $v['category_id']);
        $localArr = brandMdl::getInstance()->getBrand($v['brand_id']);
        $datas[$k]['brand_name'] = empty($localArr['brand_name']) ? '' : $localArr['brand_name']; 
    }
    require TEMPLATE_PATH.'user/products.html'; 
    die;
}



if($_GET['act'] === 'productsAdd'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('products',$uid)) ucenterNoAccess();
    import::getMdl('products');
    import::getInt('category');
    import::getLib('pscws5');
    import::getMdl('keywords');
    import::getMdl('productKeywordMaps');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['company_id'] = $uid;
        $info['is_show'] = (int)authorityInt::getInstance()->isShow();
        $info['product_name'] = empty($_POST['product_name']) ? '': trim(htmlspecialchars($_POST['product_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['product_name'])) errorAlert('产品名称不能为空');
         $info['model'] = empty($_POST['model']) ? '': trim(htmlspecialchars($_POST['model'],ENT_QUOTES,'UTF-8'));
        if(empty($info['model'])) errorAlert('产品型号不能为空');
        $cates = empty($_POST['cates']) ? errorAlert('请选择分类') : $_POST['cates'];
        $info['category_id'] = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : errorAlert('请选择分类');
        if (empty($info['category_id']))
            errorAlert('请选择分类');
        $info['brand_id'] =empty($_POST['brand_id']) ? 0: (int)$_POST['brand_id'];
        if(empty($info['brand_id'])) errorAlert('品牌不能为空');
        $info['face_pic'] = '';
        $info['market_price'] =empty($_POST['market_price']) ? 0: (int)($_POST['market_price']*100);
     
        $info['mall_price'] =empty($_POST['mall_price']) ? 0: (int)($_POST['mall_price']*100);
     
        $info['description'] = empty($_POST['description']) ? '': getValue($_POST['description']);
        if(empty($info['description'])) errorAlert('详情不能为空');
         $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $info['detail_pics'] = json_encode($detailPics);
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
           
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        $id = productsMdl::getInstance()->addProducts($info);
        if(!$id) errorAlert ('添加失败');
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['product_name']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['product_id'] = $id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            productKeywordMapsMdl::getInstance()->addProductKeywordMaps($mapinfo);
        }
        import::getInt('integral');
        integralInt::getInstance()->obtain($__INTEGRAL_GAIN['product']);
        echoJs("alert('添加成功');parent.location='".mkUrl::linkTo('user','products')."'");
        die;
    } 

    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['products'], 0);
    require TEMPLATE_PATH.'user/productsAdd.html'; 
    die;
}

if($_GET['act'] === 'productsEdit'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] && $__USER_TYPE['material'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('products',$uid)) ucenterNoAccess();
    import::getMdl('products');
    import::getInt('category');
    import::getLib('pscws5');
    import::getMdl('keywords');
    import::getMdl('productKeywordMaps');
     $id = empty ($_GET['id']) ? ucenterNoAccess () : (int)$_GET['id'];    
    $data = productsMdl::getInstance()->getProducts($id);    
    if(empty($data)) ucenterNoAccess ();
    if((int)$data['company_id']!== (int)$uid) ucenterNoAccess ();
    $data['detail_pics'] = json_decode($data['detail_pics'],true);
    if(empty($data['detail_pics'] )) $data['detail_pics']  = array();
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['product_name'] = empty($_POST['product_name']) ? '': trim(htmlspecialchars($_POST['product_name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['product_name'])) errorAlert('产品名称不能为空');
         $info['model'] = empty($_POST['model']) ? '': trim(htmlspecialchars($_POST['model'],ENT_QUOTES,'UTF-8'));
        if(empty($info['model'])) errorAlert('产品型号不能为空');
        $cates = empty($_POST['cates']) ? errorAlert('请选择分类') : $_POST['cates'];
        $info['category_id'] = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : errorAlert('请选择分类');
        if (empty($info['category_id']))
            errorAlert('请选择分类');
        $info['brand_id'] =empty($_POST['brand_id']) ? 0: (int)$_POST['brand_id'];
        if(empty($info['brand_id'])) errorAlert('品牌不能为空');
        $info['face_pic'] = $data['face_pic'];
        $info['market_price'] =empty($_POST['market_price']) ? 0: (int)($_POST['market_price']*100);
      
        $info['mall_price'] =empty($_POST['mall_price']) ? 0: (int)($_POST['mall_price'] * 100);
    
        $info['description'] = empty($_POST['description']) ? '': getValue($_POST['description']);
        if(empty($info['description'])) errorAlert('详情不能为空');
         $detailPics = empty($_POST['detailPics']) ? array() : $_POST['detailPics'];
        foreach($detailPics as $k=>$v){
            if(empty($v)) unset($detailPics[$k]);
        }
        $delpics = array();
        try{
            import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];    
                }
            }    
        }  catch (Exception $e){
            if(empty($data['face_pic'])){
                errorAlert($e->getMessage());
            }
        }
        $oldpics = empty($_POST['oldpics']) ? array() : $_POST['oldpics'];
        foreach( $data['detail_pics'] as $k=>$v){
            if(!in_array($v,$oldpics)){
              // $delpics[] = $v; 不能删除图片 
               unset($data['detail_pics'][$k]);
            }
        }
       
        $info['detail_pics'] = json_encode(array_merge($data['detail_pics'] , $detailPics));
        if(false === productsMdl::getInstance()->updateProducts($id,$info)) errorAlert ('添加失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        if($info['product_name'] != $data['product_name']){
            productKeywordMapsMdl::getInstance()->delProductKeywordMapsByProductId($product_id);

            $keywords = PSCWS5::getInstance()->getAllSplitCol($info['product_name']);
            foreach($keywords as $val){
                $mapinfo = array();
                $mapinfo['product_id'] = $id;
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
                if(!$mapinfo['keyword_id']){
                    $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
                }
                productKeywordMapsMdl::getInstance()->addProductKeywordMaps($mapinfo);
            }
        }
        echoJs("alert('修改成功');parent.location='".mkUrl::linkTo('user','productsEdit',array('id'=>$id))."'");
        die;
    } 
    import::getMdl('brandMap');
    import::getMdl('brand');
    $mapids = brandMapMdl::getInstance()->getAllBrandMap($data['category_id']); 
    $brands  = brandMdl::getInstance()->getBrandsByIds($mapids);
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['products'], $data['category_id'], true);
    require TEMPLATE_PATH.'user/productsEdit.html';
    die;
}

if($_GET['act'] === 'message'){
    import::getMdl('message');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['uid'] = $uid;

        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('称呼不能为空');
        $info['tel'] = empty($_POST['tel']) ? '': trim(htmlspecialchars($_POST['tel'],ENT_QUOTES,'UTF-8'));
        if(empty($info['tel'])) errorAlert('联系方式不能为空');
        $info['title'] = empty($_POST['title']) ? '': trim(htmlspecialchars($_POST['title'],ENT_QUOTES,'UTF-8'));
        if(empty($info['title'])) errorAlert('标题不能为空');
        $info['content'] = empty($_POST['content']) ? '': trim(htmlspecialchars($_POST['content'],ENT_QUOTES,'UTF-8'));
        if(empty($info['content'])) errorAlert('描述不能为空');
        $info['create_time'] = NOWTIME;
        
        if(!messageMdl::getInstance()->addMessage($info)) errorAlert ('操作失败');
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','message')."'");
        die;
    } 
    require TEMPLATE_PATH.'user/message.html';
    die;
}


if($_GET['act'] === 'mybids'){
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('bid',$uid)) ucenterNoAccess();
    import::getMdl('biddingLook');
    $where  = array('uid'=>$uid);
    $totalnum = biddingLookMdl::getInstance()->getBiddingLookCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');    
    $col = array('bidding_id','type');     
    $datas = biddingLookMdl::getInstance()->getBiddingLookList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','mybids',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    $local = array();
    $types = array();
    foreach($datas as $v){
        $local[] = (int)$v['bidding_id'];
        $types[$v['bidding_id']] = $v['type'];
    }
    import::getMdl('bidding');
    $datas = biddingMdl::getInstance()->getBiddingByIds($local);
    import::getMdl('biddingBid');
    $bids = biddingBidMdl::getInstance()->getBiddingBidByUidBidIds($uid,$local);
    require TEMPLATE_PATH.'user/mybids.html';
    die;
}


if($_GET['act'] === 'siteList'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('site',$uid)) ucenterNoAccess();
    import::getMdl('buildingSite');
    import::getInt('category');
    import::getInt('area');
    $url = array(); 
   
    $where  = array('company_id'=>$uid);

    $totalnum = buildingSiteMdl::getInstance()->getBuildingSiteCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`company_id`','`area_id`','status','`name`','`space_id`','`price_id`','`a_id`','`style_id`','`bg_time`','`description`');     
    $datas = buildingSiteMdl::getInstance()->getBuildingSiteList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','siteList',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    foreach($datas as $k=>$v){
        $datas[$k]['space_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['space_id']);
        $datas[$k]['style_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['style_id']);
        $datas[$k]['a_name'] = category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['a_id']);
        $datas[$k]['price_name'] =category::getInstance()->getCategoryName($__CATEGORY_TYPE['case'],$v['price_id']);
        $datas[$k]['area_name'] = areaInt::getInstance()->getAreaName($v['area_id']);
    }
    require TEMPLATE_PATH.'user/siteList.html';
    die;
}
if($_GET['act'] === 'siteStatus'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('site',$uid)) ucenterNoAccess();
    $id = empty($_GET['id']) ? ucenterNoAccess() : (int)$_GET['id'];
    import::getMdl('buildingSite');
    $siteInfo = buildingSiteMdl::getInstance()->getBuildingSite($id);
    if((int)$siteInfo['company_id']!== $uid) ucenterNoAccess ();
    
    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['status'] = empty($_POST['status']) ? errorAlert('请选择工程进度') : (int)$_POST['status'];
        if($info['status'] <= $siteInfo['status']) errorAlert ('该进度不能发布了请选择该进度后面的进度！');
        if(!isset($__SITE_STATUS_MEANS[$info['status']])) errorAlert ('请选择正确的状态');
        $info['content'] = empty($_POST['content']) ? errorAlert('请填写工程日记') : getValue($_POST['content']);
        $info['site_id'] = $id;
        import::getMdl('buildingSiteStatus');
        $ret = buildingSiteStatusMdl::getInstance()->addBuildingSiteStatus($info);
        $update =  array('status' => $info['status']);
        buildingSiteMdl::getInstance()->updateBuildingSite($id,$update);
        echoJs('alert("操作成功");parent.location="'.mkUrl::linkTo('user','siteList').'"');
        die;
    }
    require TEMPLATE_PATH.'user/siteStatus.html';
    die;
}

if($_GET['act'] === 'siteAdd'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('site',$uid)) ucenterNoAccess();
    import::getMdl('buildingSite');
    import::getInt('category');
    import::getInt('area');
    import::getMdl('designer');
    $designerPair = designerMdl::getInstance()->getAllDesignerPairByUid($uid);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['company_id'] = $uid;
        $info['create_time'] = date('Y-m-d H:i:s',NOWTIME);
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('区县不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('工地名称不能为空');
        
        $info['designer_id'] =empty($_POST['designer_id']) ? 0: (int)$_POST['designer_id'];
        if(!empty($info['designer_id'])&&!isset($designerPair[$info['designer_id']])) {
            errorAlert('请选择自己的设计师');
        }
        
        $info['space_id'] =empty($_POST['space_id']) ? 0: (int)$_POST['space_id'];
        if(empty($info['space_id'])) errorAlert('空间不能为空');
        $info['price_id'] =empty($_POST['price_id']) ? 0: (int)$_POST['price_id'];
        if(empty($info['price_id'])) errorAlert('预算不能为空');
        $info['a_id'] =empty($_POST['a_id']) ? 0: (int)$_POST['a_id'];
        if(empty($info['a_id'])) errorAlert('面积不能为空');
        $info['style_id'] =empty($_POST['style_id']) ? 0: (int)$_POST['style_id'];
        if(empty($info['style_id'])) errorAlert('风格不能为空');
        $info['bg_time'] = empty($_POST['bg_time']) ? '': trim(htmlspecialchars($_POST['bg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['bg_time'])) errorAlert('开始时间不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(empty($info['description'])) errorAlert('描述不能为空');
        $info['is_show'] = (int)authorityInt::getInstance()->isShow();
        $info['face_pic'] = '';
        try{
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],260,240);
            if(!empty($face_pic['web_file_name'])) $info['face_pic'] = $face_pic['web_file_name'];
            
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        
        
        if(!buildingSiteMdl::getInstance()->addBuildingSite($info)) errorAlert ('操作失败');
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','siteList')."'");
        die;
    } 
    $spaces = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['space']);
    $styles = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['style']);
    $areas = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['area']);
    $prices = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['price']);
    $quxian =areaInt::getInstance()->getAreas();
    
    require TEMPLATE_PATH.'user/siteAdd.html';
    die;
}

if($_GET['act'] === 'siteEdit'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
     import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('site',$uid)) ucenterNoAccess();
    import::getMdl('buildingSite');
    import::getInt('category');
    import::getInt('area');
   
    $id = empty ($_GET['id']) ? ucenterNoAccess (): (int)$_GET['id'];    
    $data = buildingSiteMdl::getInstance()->getBuildingSite($id);    
    if(empty($data))  ucenterNoAccess ();
    if((int)$data['company_id']!== $uid ) ucenterNoAccess ();
    import::getMdl('designer');
    $designerPair = designerMdl::getInstance()->getAllDesignerPairByUid($uid);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      
        $info['area_id'] =empty($_POST['area_id']) ? 0: (int)$_POST['area_id'];
        if(empty($info['area_id'])) errorAlert('区县不能为空');
        $info['name'] = empty($_POST['name']) ? '': trim(htmlspecialchars($_POST['name'],ENT_QUOTES,'UTF-8'));
        if(empty($info['name'])) errorAlert('工地名称不能为空');
        $info['space_id'] =empty($_POST['space_id']) ? 0: (int)$_POST['space_id'];
        if(empty($info['space_id'])) errorAlert('空间不能为空');
        
        
        $info['designer_id'] =empty($_POST['designer_id']) ? 0: (int)$_POST['designer_id'];
        if(!empty($info['designer_id'])&&!isset($designerPair[$info['designer_id']])) {
            errorAlert('请选择自己的设计师');
        }
        
        $info['price_id'] =empty($_POST['price_id']) ? 0: (int)$_POST['price_id'];
        if(empty($info['price_id'])) errorAlert('预算不能为空');
        $info['a_id'] =empty($_POST['a_id']) ? 0: (int)$_POST['a_id'];
        if(empty($info['a_id'])) errorAlert('面积不能为空');
        $info['style_id'] =empty($_POST['style_id']) ? 0: (int)$_POST['style_id'];
        if(empty($info['style_id'])) errorAlert('风格不能为空');
        $info['bg_time'] = empty($_POST['bg_time']) ? '': trim(htmlspecialchars($_POST['bg_time'],ENT_QUOTES,'UTF-8'));
        if(empty($info['bg_time'])) errorAlert('开始时间不能为空');
        $info['description'] = empty($_POST['description']) ? '': trim(htmlspecialchars($_POST['description'],ENT_QUOTES,'UTF-8'));
        if(empty($info['description'])) errorAlert('描述不能为空');
        $info['face_pic'] = $data['face_pic'];
        $delpics = array();
         try{
            import::getLib('uploadimg');
            if(!empty($_FILES['face_pic']['tmp_name'])){
                $face_pic = uploadImg::getInstance()->upload('face_pic');
                if(!empty($face_pic['web_file_name'])) {
                    uploadImg::getInstance()->resizeImage($face_pic['store_file_name'],$face_pic['store_file_name'],260,240);
                    $info['face_pic'] = $face_pic['web_file_name'];
                    $delpics[] = $data['face_pic'];
                }    
            }
           
        }  catch (Exception $e){
            errorAlert($e->getMessage());
        }
        if(false === buildingSiteMdl::getInstance()->updateBuildingSite($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','siteList')."'");
        die;
    } 
    
    $spaces = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['space']);
    $styles = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['style']);
    $areas = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['area']);
    $prices = category::getInstance()->getChildCol($__CATEGORY_TYPE['case'],$__CASE_CATEGORY['price']);
    $quxian =areaInt::getInstance()->getAreas();
    require TEMPLATE_PATH.'user/siteEdit.html';
    die;
}

if($_GET['act'] === 'siteView'){
    if($__USER_TYPE['company'] !== (int)$__USER_INFO['type'] ) ucenterNoAccess ();
    import::getMdl('buildingSite');
    import::getMdl('buildingSiteApply');
    $url = array();
    $where = array();
    $id = empty($_GET['id']) ? ucenterNoAccess() : (int)$_GET['id'];
    $data = buildingSiteMdl::getInstance()->getBuildingSite($id);    
    if(empty($data))  ucenterNoAccess ();
    if((int)$data['company_id']!== $uid ) ucenterNoAccess ();
    $where['site_id'] = $id;
    $url['id'] = $id;  
    $totalnum = buildingSiteApplyMdl::getInstance()->getBuildingSiteApplyCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;   
    $orderby = array('id'=>'DESC');  
    $col = array('`id`','`name`','`phone`','`comment`');     
    $datas = buildingSiteApplyMdl::getInstance()->getBuildingSiteApplyList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','siteList',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/siteView.html';
    die;
}


if($_GET['act'] === 'quantityRoom'){
    import::getMdl('quantityRoom');
    $where  = array('company_id'=>$uid);
    $totalnum = quantityRoomMdl::getInstance()->getQuantityRoomCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.`id`'=>'DESC');  
    $col = array('a.`id`','a.`name`','a.`tel`','a.`date`','a.`description`','a.`create_time`');     
    $datas = quantityRoomMdl::getInstance()->getQuantityRoomList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','quantityRoom',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/quantityRoom.html';
    die;
}

if($_GET['act'] === 'bookingDesign'){
    import::getMdl('bookingDesign');
    $where  = array('company_id'=>$uid);
    $totalnum = bookingDesignMdl::getInstance()->getBookingDesignCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    $orderby = array('id'=>'DESC');   
    $col = array('`id`','`designer_id`','`name`','`tel`','`date`','`description`');     
    $datas = bookingDesignMdl::getInstance()->getBookingDesignList($col,$where,$orderby,$begin,PAGE_SIZE);
    $ids = array();
    foreach($datas as $v){
        $ids[$v['designer_id']] = $v['designer_id'];
    }
    $links = createPage(mkUrl::linkTo('user','bookingDesign',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    import::getMdl('designer');
    $designers = designerMdl::getInstance()->getDesignerByIds($ids);
    require TEMPLATE_PATH.'user/bookingDesign.html';
    die;
}

if($_GET['act'] === 'biddingBidAdd'){
    if($__USER_TYPE['owner'] === (int)$__USER_INFO['type'] ) dieJs ("alert('您的用户类型不允许操作此内容');location='".mkUrl::linkTo('tenders')."';");
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('bid',$uid)) ucenterNoAccess();
    
    $bid = empty($_GET['bid']) ? ucenterNoAccess() : (int)$_GET['bid'];
    import::getMdl('bidding');
    $bidding = biddingMdl::getInstance()->getBidding($bid);
    if(empty($bidding)) ucenterNoAccess ();
    if(!$bidding['is_show']) ucenterNoAccess ();
    if(!empty($bidding['bid_id'])) dieJs ("alert('标已结束');location='".mkUrl::linkTo('tenders')."';");
    import::getMdl('biddingBid');
    $biddingBid = biddingBidMdl::getInstance()->getBiddingBidByUidBidId($uid,$bid);
    if(!empty($biddingBid)) dieJs ("alert('您已经投过标了！可以修改您的投标信息！');location='".mkUrl::linkTo('user','biddingBidEdit',array('bid'=>$bid))."';");
    $isVip =  usersMdl::getInstance()->checkIsVip($uid);;
   
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['bid'] =$bid;
        $info['uid'] = $uid;
        $info['main_material'] =empty($_POST['main_material']) ? 0: (int)($_POST['main_material']*100);
        if(empty($info['main_material'])) errorAlert('主材料不能为空');
        $info['vice_material'] =empty($_POST['vice_material']) ? 0: (int)($_POST['vice_material']*100);
        if(empty($info['vice_material'])) errorAlert('辅材料不能为空');
        $info['artificial'] =empty($_POST['artificial']) ? 0: (int)($_POST['artificial']*100);
        if(empty($info['artificial'])) errorAlert('人工不能为空');
        $info['management'] =empty($_POST['management']) ? 0: (int)($_POST['management']*100);
        if(empty($info['management'])) errorAlert('管理不能为空');
        $info['is_show'] = (int)authorityInt::getInstance()->isShow();
        $info['design'] =empty($_POST['design']) ? 0: (int)($_POST['design']*100);
        if(empty($info['design'])) errorAlert('设计不能为空');
        $info['detail_pics'] = '';
        $info['message'] = empty($_POST['message']) ? '': trim(htmlspecialchars($_POST['message'],ENT_QUOTES,'UTF-8'));
        $info['total'] = $info['main_material'] + $info['vice_material'] + $info['artificial'] +$info['management'] +  $info['design'];
        $info['t'] = NOWTIME;
        if(empty($info['message'])) errorAlert('留言不能为空');
        if($isVip){
            try{
                import::getLib('uploadimg');
                $detail = uploadImg::getInstance()->upload('details');
                if(!empty($detail['web_file_name'])) $info['detail_pics'] = json_encode ($detail['web_file_name']);

            }  catch (Exception $e){
                errorAlert($e->getMessage());
            }
        }
        if(!biddingBidMdl::getInstance()->addBiddingBid($info)) errorAlert ('操作失败');
        $count = biddingBidMdl::getInstance()->getBiddingBidCount(array('bid'=>$bid));
        biddingMdl::getInstance()->updateBidding($bid,array('bid_num'=>$count));
        import::getMdl('biddingLook');
        $has = biddingLookMdl::getInstance()->getBiddingLook($uid,$bid);
        if(!$has){
            biddingLookMdl::getInstance()->addBiddingLook(array('uid'=>$uid,'bidding_id'=>$bid,'type'=>0));
        }
        
        import::getInt('sms');
        import::getMdl('company');
        smsInt::getInstance()->send('bid',array($bidding['mobile']),array('name'=>$bidding['name'],'company'=> companyMdl::getInstance()->getCompanyName($uid),'site_name'=>$__SETTING['site_name']));
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','biddingBidEdit',array('bid'=>$bid))."'");
        die;
    } 
    
    require TEMPLATE_PATH.'user/biddingBidAdd.html';
    die;
}
if($_GET['act'] === 'biddingBidEdit'){
    if($__USER_TYPE['owner'] === (int)$__USER_INFO['type'] ) dieJs ("alert('您的用户类型不允许操作此内容');location='".mkUrl::linkTo('tenders')."';");
    import::getInt('authority');
    if(!authorityInt::getInstance()->isAuthority('bid',$uid)) ucenterNoAccess();
    $bid = empty($_GET['bid']) ? ucenterNoAccess() : (int)$_GET['bid'];
    import::getMdl('bidding');
    $bidding = biddingMdl::getInstance()->getBidding($bid);
    if(empty($bidding)) ucenterNoAccess ();
    if(!$bidding['is_show']) ucenterNoAccess ();
    if(!empty($bidding['bid_id'])) dieJs ("alert('标已结束');location='".mkUrl::linkTo('tenders')."';");
    import::getMdl('biddingBid');
    $data = biddingBidMdl::getInstance()->getBiddingBidByUidBidId($uid,$bid);
    $data['detail_pics'] = json_decode($data['detail_pics'],true);
    $data['detail_pics'] = empty($data['detail_pics']) ?  array() : $data['detail_pics'];
    $id = (int)$data['id'];
    if(empty($data)) dieJs ("alert('您还未投标');location='".mkUrl::linkTo('tenders')."';");
    $isVip =  usersMdl::getInstance()->checkIsVip($uid);;
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $info['bid'] = $bid;
        $info['uid'] = $uid;
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
        if($isVip){
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
                //$delpics[] = $v;
                unset($data['detail_pics'][$k]);
                }
            }

            $info['detail_pics'] = json_encode(array_merge($data['detail_pics'] , $detail_pics));
        }
        if(false === biddingBidMdl::getInstance()->updateBiddingBid($id,$info)) errorAlert ('操作失败');
        foreach($delpics as $v){
            if(file_exists(BASE_PATH.$v)) unlink (BASE_PATH.$v);
        }
     
        echoJs("alert('操作成功');parent.location='".mkUrl::linkTo('user','biddingBidEdit',array('bid'=>$bid))."'");
        die;
    } 
    
    require TEMPLATE_PATH.'user/biddingBidEdit.html';
    die;
}



if($_GET['act'] === 'integralUsed'){
    import::getMdl('integralUsed');
    $totalnum = integralUsedMdl::getInstance()->getIntegralUsedCount(array('uid'=>$uid));
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.`id`'=>'DESC');   
    $col = array('a.`id`','a.`type`','a.`num`','a.`t`');     
    $datas = integralUsedMdl::getInstance()->getIntegralUsedList($col,array('uid'=>$uid),$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','integralUsed',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/integralUsed.html';
    die;
}

if($_GET['act'] === 'integral'){
    import::getMdl('integral');
    $totalnum = integralMdl::getInstance()->getIntegralCount(array('uid'=>$uid));
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.`id`'=>'DESC'); 
    $col = array('a.`id`','a.`type`','a.`num`','a.`expires_t`','a.`t`');     
    $datas = integralMdl::getInstance()->getIntegralList($col,array('uid'=>$uid),$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','integral',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    $integralSetting = import::getCfg('integral');
    require TEMPLATE_PATH.'user/integral.html';
    die;
}

if($_GET['act'] === 'exchange'){
    import::getMdl('integralExchange');

    $totalnum = integralExchangeMdl::getInstance()->getIntegralExchangeCount(array('uid'=>$uid));
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('a.id'=>'DESC');  
    $col = array('a.`id`','c.product_name','a.`type`','a.`integral`','a.`t`','a.`status`');     
    $datas = integralExchangeMdl::getInstance()->getIntegralExchangeList($col,array('uid'=>$uid),$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('user','exchange',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'user/exchange.html';
    die;
}

function ucenterNoAccess(){
    require TEMPLATE_PATH.'user/ucenterNoAccess.html';
    die;
}