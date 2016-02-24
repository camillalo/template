<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE', 50);
import::getInt('category');
import::getMdl('content');
import::getMdl('contentTagmap');
import::getMdl('contentTag');
import::getInt('recommend');
recommend::getInstance()->init(3);
if($_GET['act'] === 'main'){
    
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    $tags = contentTagmapMdl::getInstance()->getHotTagIds(20);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('content');
    require TEMPLATE_PATH. 'content.html';
    die;
}
if($_GET['act'] === 'list'){
    $__SETTING['title'] .= '家居学堂';
    $url = array();
    $where = array();
    $leftCates = array();
   import::getMdl('case');
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    $cases =  caseMdl::getInstance()->getNewCase(5);
    $_GET['cate_id'] = empty($_GET['cate_id']) ? 0 : (int)$_GET['cate_id'];
    if (!empty($_GET['cate_id'])) {
        $cate_info = category::getInstance()->getCategory($__CATEGORY_TYPE['contents'],$_GET['cate_id']);
        if(empty($cate_info)) show404 ();
        $__SETTING['title'] .= '('.$cate_info['category_name'].')';
        $lastIds = category::getInstance()->getAllLastChildIds($__CATEGORY_TYPE['contents'],$_GET['cate_id']);
        $url['cate_id'] =  $_GET['cate_id'];
        if(!empty($lastIds)){
            $where['last_category_id'] = $lastIds;
            $leftCates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$_GET['cate_id']);
        }else{
            $where['category_id'] = $_GET['cate_id'];
            $leftCates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$cate_info['parent_id']);
        }
        
    }else{
        $leftCates = $cates;
    }
    $totalnum = contentMdl::getInstance()->getContentCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
    $pageMax = ceil($totalnum / PAGE_SIZE);
    if ($_GET['page'] > $pageMax)
        $_GET['page'] = $pageMax;
    if ($_GET['page'] <= 0)
        $_GET['page'] = 1;
    $begin = ($_GET['page'] - 1) * PAGE_SIZE;

    $orderby = array('content_id' => 'DESC');
    $col = array('`content_id`', '`title`','`face_pic`' ,'`description`' , '`pv_num`', '`create_time`', '`keywords`');
    $datas = contentMdl::getInstance()->getContentList($col, $where, $orderby, $begin, PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('content','list',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    
    $tags = contentTagmapMdl::getInstance()->getHotTagIds(50);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('contentlist',array('catename'=> category::getInstance()->getCategoryName($__CATEGORY_TYPE['contents'],$_GET['cate_id']) ));
    require TEMPLATE_PATH.'content_list.html';
    die;
}

if($_GET['act'] === 'tag'){
    $__SETTING['title'] .= '家居学堂';
    $url = array();
    $where = array();
    $_GET['tag_id'] = empty($_GET['id']) ? show404() : (int)$_GET['id'];
    $tag = contentTagMdl::getInstance()->getContentTag($_GET['tag_id']);
    if(!empty($tag)) {
        $where['tag_id'] = $_GET['tag_id'];
        $__SETTING['title'] .= '('.$tag['tag'].')';
        $url['id'] = $_GET['tag_id'];
    }    
    else {      
        show404 ();
    }
    
    $totalnum = contentTagmapMdl::getInstance()->getContentTagMapCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
    $pageMax = ceil($totalnum / PAGE_SIZE);
    if ($_GET['page'] > $pageMax)
        $_GET['page'] = $pageMax;
    if ($_GET['page'] <= 0)
        $_GET['page'] = 1;
    $begin = ($_GET['page'] - 1) * PAGE_SIZE;

    $orderby = array('a.content_id' => 'DESC');
    $col = array('b.`content_id`', 'b.`title`','b.`face_pic`' ,'b.`description`' , 'b.`pv_num`', 'b.`create_time`', 'b.`keywords`');
    $datas = contentTagmapMdl::getInstance()->getContentTagMapList($col, $where, $orderby, $begin, PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('content','tag',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
    
    $tags = contentTagmapMdl::getInstance()->getHotTagIds(50);
    $leftCates = $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    import::getMdl('case');
    $cases =  caseMdl::getInstance()->getNewCase(5);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('contentlist',array('catename'=> $tag['tag'] ));
    require TEMPLATE_PATH.'content_list.html';
    die;
}


if($_GET['act'] === 'detail'){
    $content_id = empty($_GET['id']) ? show404(mkUrl::linkTo('content')) : (int) $_GET['id'];
    $data = contentMdl::getInstance()->getContent($content_id);
    if (empty($data)) show404 (mkUrl::linkTo('content'));
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    $tags = contentTagmapMdl::getInstance()-> getAllContentTagmap($content_id);
    
    $__SETTING['title'] = $data['title'];
    $__SETTING['keyword'] = $data['keywords'];
    $__SETTING['description'] = $data['description'];
    $topData = contentMdl::getInstance()->getTopContent($content_id);
    $nextData = contentMdl::getInstance()->getNextContent($content_id);
    contentMdl::getInstance()->updateContentPv($content_id);
    import::getMdl('case');
    $cases = caseMdl::getInstance()->getNewCase(5);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('contentshow',array('title'=> $data['title'] ,'description'=> $data['description'] ,'keywords'=> $data['keywords'] ));
    require TEMPLATE_PATH. 'content_detail.html';
    die;
}
if($_GET['act'] === 'system'){
    $content_id = empty($_GET['id']) ? show404(mkUrl::linkTo('content')) : (int) $_GET['id'];
    $data = contentMdl::getInstance()->getContent($content_id);
    if (empty($data)) show404 (mkUrl::linkTo('content'));
    $__SETTING['title'] = $data['title'];
    $__SETTING['keyword'] = $data['keywords'];
    $__SETTING['description'] = $data['description'];
    $systemContents = contentMdl::getInstance()->getContentsByCateId($__PINDAO_ROOT['system'],12,'ASC'); 
    require TEMPLATE_PATH. 'system_content.html';
    die;
}

if($_GET['act']==='diary'){
    import::getMdl('diary');
    $where = array('is_show'=>1);
    $cate_id = empty($_GET['id']) ? 0 : (int)$_GET['id'];
    if(!empty($cate_id)){
        $where['cate_id'] = $cate_id;
    }
    $totalnum = diaryMdl::getInstance()->getDiaryCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('id'=>'DESC'); 
    $col = array('`id`','`title`','contents','`cate_id`');     
    $datas = diaryMdl::getInstance()->getDiaryList($col,$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('content','diary',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    
    
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    $diary_cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['lc']);
    
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('diary');
    require TEMPLATE_PATH. 'content_diary.html';
    die;
}


if($_GET['act'] === 'diaryshow'){
    import::getMdl('diary');
    $id = empty ($_GET['id']) ? show404(): (int)$_GET['id'];    
    $data = diaryMdl::getInstance()->getDiary($id);  
    import::getMdl('case');
    $cases =  caseMdl::getInstance()->getNewCase(5);
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('diaryshow',array('title'=> $data['title'] ));
    require TEMPLATE_PATH. 'content_diayshow.html';
    die;
}