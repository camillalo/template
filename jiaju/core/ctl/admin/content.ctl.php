<?php

if (!defined('BASE_PATH')) {
    exit('Access Denied');
}
session_write_close();
define('PAGE_SIZE', 30); //分页大小
import::getMdl('content');
import::getInt('category');
import::getLib('pscws5');
import::getMdl('contentTag');
import::getMdl('contentTagmap');
import::getMdl('keywords');
import::getMdl('contentKeywordMaps');
if ($_GET['act'] === 'main') {
    $url = 'index.php?ctl=content&act=main';
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8');
    $where = array();
    if (!empty($_GET['keyword'])) {
        $url.='&keyword=' . urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }
    $cates = empty($_GET['cates']) ? array() : $_GET['cates'];
    
    $category_id = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : 0;
    
    if(empty($category_id)) $category_id = isset($cates[count($cates) - 2]) ? (int) $cates[count($cates) - 2] : 0;
    
    if(isset($_GET['category_id'])) $category_id = (int)$_GET['category_id'];
    if (!empty($category_id)) {
        $lastIds = category::getInstance()->getAllLastChildIds($__CATEGORY_TYPE['contents'],$category_id);
        $url.='&category_id=' . $category_id;
        if(!empty($lastIds)){
            $where['last_category_id'] = $lastIds;
        }else{
            $where['category_id'] = $category_id;
        }
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
    $col = array('`content_id`', '`title`', '`category_id`', '`source`', '`author`', '`create_time`', '`keywords`');
    $datas = contentMdl::getInstance()->getContentList($col, $where, $orderby, $begin, PAGE_SIZE);
    $links = createPage($url . '&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    foreach ($datas as $k => $v) {
        $localArr = category::getInstance()->getCategory($__CATEGORY_TYPE['contents'], $v['category_id']);
        $datas[$k]['category_name'] = empty($localArr['category_name']) ? '' : $localArr['category_name'];
    }
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['contents'], $category_id, true);
    logsInt::getInstance()->systemLogs('查看了文章列表');
    require TEMPLATE_PATH . 'content/main.html';
    die;
}



if ($_GET['act'] === 'edit') {
    $content_id = empty($_GET['content_id']) ? errorAlert('参数错误') : (int) $_GET['content_id'];
    $data = contentMdl::getInstance()->getContent($content_id);
    if (empty($data))
        errorAlert('参数出错');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cates = empty($_POST['cates']) ? errorAlert('请选择分类') : $_POST['cates'];
        $info['category_id'] = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : errorAlert('请选择分类');
        if (empty($info['category_id']))
            errorAlert('请选择分类');
        $info['title'] = empty($_POST['title']) ? errorAlert('文章标题不能为空') : trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
        $info['author'] = empty($_POST['author']) ? '' : trim(htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'));
        $info['source'] = empty($_POST['source']) ? '' : trim(htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8'));
        $info['keywords'] = empty($_POST['keywords']) ? errorAlert('关键字不能为空') : trim(htmlspecialchars($_POST['keywords'], ENT_QUOTES, 'UTF-8'));
        $info['description'] = empty($_POST['description']) ?errorAlert('导读描述不能为空') : trim(htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8'));
        $info['contents'] = empty($_POST['content']) ? '' : $_POST['content']; //需要HTML 代码 可能还有JS
        $info['create_time'] = date('Y-m-d H:i:s', NOWTIME);
        $info['face_pic'] = $data['face_pic'];
        try {
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if (!empty($face_pic['web_file_name']))
                $info['face_pic'] = $face_pic['web_file_name'];
        } catch (Exception $e) {
            
        }

        if (false === contentMdl::getInstance()->updateContent($content_id, $info))
            errorAlert('更新失败');
        $tags = preg_split('/[,，|、]/uim', $info['keywords']);
        contentTagmapMdl::getInstance()->delContentTagmapByContentId($content_id);
        foreach($tags as $v){
            $mapinfo = array();
            $mapinfo['content_id'] = $content_id; 
            $mapinfo['tag_id'] = contentTagMdl::getInstance()->getContentTagIdByTagname($v);
            if(!$mapinfo['tag_id']) {
                $mapinfo['tag_id'] = contentTagMdl::getInstance()->addContentTag( array('tag'=>$v));
            }
            contentTagmapMdl::getInstance()->replaceContentTagmap($mapinfo);
        }
        if($info['title'] != $data['title']){
            contentKeywordMapsMdl::getInstance()->delContentKeywordMapsByContentId($content_id);

            $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
            foreach($keywords as $val){
                $mapinfo = array();
                $mapinfo['content_id'] = $content_id;
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
                if(!$mapinfo['keyword_id']){
                    $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
                }
                contentKeywordMapsMdl::getInstance()->addContentKeywordMaps($mapinfo);
            }
        }
        if (!empty($data['face_pic'])) {
            if (file_exists(BASE_PATH . $data['face_pic']))
                unlink(BASE_PATH . $data['face_pic']);
        }
        logsInt::getInstance()->systemLogs('修改了文章内容',$data,$info);
        errorAlert('操作成功');
        die;
    }
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['contents'], $data['category_id'], true);
    logsInt::getInstance()->systemLogs('打开了文章内容编辑面板');
    require TEMPLATE_PATH . 'content/edit.html';
    die;
}

if ($_GET['act'] === 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cates = empty($_POST['cates']) ? errorAlert('请选择分类') : $_POST['cates'];
        $info['category_id'] = isset($cates[count($cates) - 1]) ? (int) $cates[count($cates) - 1] : errorAlert('请选择分类');
        if (empty($info['category_id']))
            errorAlert('请选择分类');
        $info['title'] = empty($_POST['title']) ? errorAlert('文章标题不能为空') : trim(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'));
        $info['author'] = empty($_POST['author']) ? '' : trim(htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'));
        $info['source'] = empty($_POST['source']) ? '' : trim(htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8'));
        $info['keywords'] = empty($_POST['keywords']) ? errorAlert('关键字不能为空') :  trim(htmlspecialchars($_POST['keywords'], ENT_QUOTES, 'UTF-8'));
        $info['description'] = empty($_POST['description'])  ? errorAlert('导读描述不能为空') :  trim(htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8'));
        $info['contents'] = empty($_POST['content']) ? '' : $_POST['content'];
        $info['face_pic'] = '';
        $info['create_time'] = date('Y-m-d H:i:s', NOWTIME);
        try {
            import::getLib('uploadimg');
            $face_pic = uploadImg::getInstance()->upload('face_pic');
            if (!empty($face_pic['web_file_name']))
                $info['face_pic'] = $face_pic['web_file_name'];
        } catch (Exception $e) {
            
        }
        $content_id = contentMdl::getInstance()->addContent($info);
        if (!$content_id)
            errorAlert('添加失败');
        $tags = preg_split('/[,，|、]/uim', $info['keywords']);
       
        foreach($tags as $v){
            $mapinfo = array();
            $mapinfo['content_id'] = $content_id;
            
            $mapinfo['tag_id'] = contentTagMdl::getInstance()->getContentTagIdByTagname($v);
            if(!$mapinfo['tag_id']) {
                $mapinfo['tag_id'] = contentTagMdl::getInstance()->addContentTag( array('tag'=>$v));
            }
            contentTagmapMdl::getInstance()->replaceContentTagmap($mapinfo);
        }
        
        $keywords = PSCWS5::getInstance()->getAllSplitCol($info['title']);
        foreach($keywords as $val){
            $mapinfo = array();
            $mapinfo['content_id'] = $content_id;
            $mapinfo['keyword_id'] = keywordsMdl::getInstance()->getKeywordsIdByKeyword($val);
           // echo  $mapinfo['keyword_id'];
            if(!$mapinfo['keyword_id']){
                $mapinfo['keyword_id'] = keywordsMdl::getInstance()->addKeywords(array('keyword'=>$val));
            }
            contentKeywordMapsMdl::getInstance()->addContentKeywordMaps($mapinfo);
        }
        logsInt::getInstance()->systemLogs('新增了文章内容',$info,array());
        echoJs("alert('添加成功');parent.location='index.php?ctl=content&act=main'");
        die;
    }
    $select = category::getInstance()->getSelect($__CATEGORY_TYPE['contents'], 0);
    require TEMPLATE_PATH . 'content/add.html';
    die;
}


if ($_GET['act'] === 'del') {
    $content_id = empty($_GET['content_id']) ? (empty($_GET['id']) ? errorAlert('参数错误') : $_GET['id']):  $_GET['content_id'];
    $ids = array();
    if(is_array($content_id)){
        foreach($content_id as $v){
            $ids[] = (int)$v;
        }
    }else{
        $ids [] = (int)$content_id;
    }
    $data = array();
    foreach($ids as $id){
        $data = contentMdl::getInstance()->getContent($id);
        if (false !== contentMdl::getInstance()->delContent($id)) {
            logsInt::getInstance()->systemLogs('删除了文章内容',$data,array());
            if (!empty($data['face_pic'])) {
                if (file_exists(BASE_PATH . $data['face_pic']))
                    unlink(BASE_PATH . $data['face_pic']);
            }
            contentTagmapMdl::getInstance()->delContentTagmapByContentId($id);
            contentKeywordMapsMdl::getInstance()->delContentKeywordMapsByContentId($id);   
        }
    }
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=content' : $_GET['back_url'];
    dieJs('alert("操作成功");parent.location="' . $back_url . '"');
    die;
}

