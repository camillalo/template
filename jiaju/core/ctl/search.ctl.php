<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE', 15);
import::getLib('pscws5');
import::getMdl('keywords');
//文章搜索 
if($_GET['act'] === 'content'){
    import::getMdl('contentKeywordMaps');
    import::getMdl('content');
    
    $url = array();
    $searchWord = empty($_GET['word']) ? show404() : htmlspecialchars($_GET['word'],ENT_QUOTES,'UTF-8');
    $url['word'] = $searchWord; 
    $words = PSCWS5::getInstance()->getAllSplitCol($searchWord);
    if(empty($words)) show404 ();
    $words2 = html::wordsChangeColor($words);
    
    $keywordIds = keywordsMdl::getInstance()->getIdsByKeywords($words);
    if(!empty($keywordIds)){
        $t1 = microtime(true);
        $totalnum = contentKeywordMapsMdl::getInstance()->getContentKeywordMapsCount($keywordIds);
        $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        $pageMax = ceil($totalnum / PAGE_SIZE);
        $pageMax = $pageMax > 20 ? 20 : $pageMax;
        if ($_GET['page'] > $pageMax)
            $_GET['page'] = $pageMax;
        if ($_GET['page'] <= 0)
            $_GET['page'] = 1;
        
        $begin = ($_GET['page'] - 1) * PAGE_SIZE;
        $contentIds = contentKeywordMapsMdl::getInstance()->getContentKeywordMapsList($keywordIds,$begin,PAGE_SIZE);
        $datas = contentMdl::getInstance()->getContentsByIds($contentIds); //IN 查询出来的排序不是自己想要的 所以做了微小处理  
        $showDatas = array();
        foreach($datas as $val){
            $showDatas[$val['content_id']] = $val;
        }
        $links = createPage(mkUrl::linkTo('search','content',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
        $showTime = round( microtime(true) - $t1 ,4);
    }
    $defaultSearch = 6;
    import::getInt('category');
    $cates = category::getInstance()->getChildCol($__CATEGORY_TYPE['contents'],$__PINDAO_ROOT['jj']);
    $_GET['ctl'] = 'content';//改变导航的选中状态
    require  TEMPLATE_PATH .'content_search.html';
    die;
}

if($_GET['act'] === 'company'){
    $url = array();
    $searchWord = empty($_GET['word']) ? show404() : htmlspecialchars($_GET['word'],ENT_QUOTES,'UTF-8');
    $url['word'] = $searchWord; 
    $words = PSCWS5::getInstance()->getAllSplitCol($searchWord);
    if(empty($words)) show404 ();
    $words2 = html::wordsChangeColor($words);
    $keywordIds = keywordsMdl::getInstance()->getIdsByKeywords($words);
    
    import::getMdl('companyKeywordMaps');
    import::getMdl('company');
    
    if(!empty($keywordIds)){
        $t1 = microtime(true);
        $totalnum = companyKeywordMapsMdl::getInstance()->getCompanyKeywordMapsCount($keywordIds);
        $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        $pageMax = ceil($totalnum / PAGE_SIZE);
        $pageMax = $pageMax > 20 ? 20 : $pageMax;
        if ($_GET['page'] > $pageMax)
            $_GET['page'] = $pageMax;
        if ($_GET['page'] <= 0)
            $_GET['page'] = 1;
        $begin = ($_GET['page'] - 1) * PAGE_SIZE;
        $companyIds = companyKeywordMapsMdl::getInstance()->getCompanyKeywordMapsList($keywordIds,$begin,PAGE_SIZE);
        //print_r($companyIds);
        //print_r(mysql::getInstance()->getSqls());
        $datas = companyMdl::getInstance()->getCompanysByIds($companyIds);   
        $showDatas = array();
        foreach($datas as $val){
            $showDatas[$val['uid']] = $val;
        }
        
        $links = createPage(mkUrl::linkTo('search','company',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
        $showTime = round( microtime(true) - $t1 ,4);
    }
    
    
    $defaultSearch = 0;
    $_GET['ctl'] = 'company';//改变导航的选中状态
    require  TEMPLATE_PATH .'company_search.html';
    die;
}



if($_GET['act'] === 'preferential'){
    $url = array();
    $searchWord = empty($_GET['word']) ? show404() : htmlspecialchars($_GET['word'],ENT_QUOTES,'UTF-8');
    $url['word'] = $searchWord; 
    $words = PSCWS5::getInstance()->getAllSplitCol($searchWord);
    if(empty($words)) show404 ();
    $words2 = html::wordsChangeColor($words);
    $keywordIds = keywordsMdl::getInstance()->getIdsByKeywords($words);
    
    import::getMdl('preferentialKeywordMaps');
    import::getMdl('preferential');
    
    if(!empty($keywordIds)){
        $t1 = microtime(true);
        $totalnum = preferentialKeywordMapsMdl::getInstance()->getPreferentialKeywordMapsCount($keywordIds);
        $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        $pageMax = ceil($totalnum / PAGE_SIZE);
        $pageMax = $pageMax > 20 ? 20 : $pageMax;
        if ($_GET['page'] > $pageMax)
            $_GET['page'] = $pageMax;
        if ($_GET['page'] <= 0)
            $_GET['page'] = 1;
        $begin = ($_GET['page'] - 1) * PAGE_SIZE;
        $preferentialIds = preferentialKeywordMapsMdl::getInstance()->getPreferentialKeywordMapsList($keywordIds,$begin,PAGE_SIZE);
        $datas = preferentialMdl::getInstance()->getPreferentialsByIds($preferentialIds);   
        $showDatas = array();
        foreach($datas as $val){
            $showDatas[$val['id']] = $val;
        }
        $links = createPage(mkUrl::linkTo('search','preferential',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
        $showTime = round( microtime(true) - $t1 ,4);
    }
    
    
    $defaultSearch = 2;
    $_GET['ctl'] = 'service';//改变导航的选中状态
    $_GET['act'] = 'preferential';
    require  TEMPLATE_PATH .'preferential_search.html';
    die;
}



if($_GET['act'] === 'ask'){
    import::getMdl('ask');
    import::getMdl('askKeywordMaps');
    $url = array();
    $searchWord = empty($_GET['word']) ? show404() : htmlspecialchars($_GET['word'],ENT_QUOTES,'UTF-8');
    $url['word'] = $searchWord; 
    $words = PSCWS5::getInstance()->getAllSplitCol($searchWord);
    if(empty($words)) show404 ();
    $words2 = html::wordsChangeColor($words);
    $keywordIds = keywordsMdl::getInstance()->getIdsByKeywords($words);
   
    if(!empty($keywordIds)){
        $t1 = microtime(true);
        $totalnum = askKeywordMapsMdl::getInstance()->getAskKeywordMapsCount($keywordIds);
        $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        $pageMax = ceil($totalnum / PAGE_SIZE);
        $pageMax = $pageMax > 20 ? 20 : $pageMax;
        if ($_GET['page'] > $pageMax)
            $_GET['page'] = $pageMax;
        if ($_GET['page'] <= 0)
            $_GET['page'] = 1;
        $begin = ($_GET['page'] - 1) * PAGE_SIZE;
        $askIds = askKeywordMapsMdl::getInstance()->getAskKeywordMapsList($keywordIds,$begin,PAGE_SIZE);
        $datas = askMdl::getInstance()->getAsksByIds($askIds);   
        $showDatas = array();
        foreach($datas as $val){
            $showDatas[$val['id']] = $val;
        }
        
        $links = createPage(mkUrl::linkTo('search','ask',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
        $showTime = round( microtime(true) - $t1 ,4);
       
    }
    $defaultSearch = 8;
    $_GET['ctl'] = 'ask';//改变导航的选中状态
    require TEMPLATE_PATH.'ask_search.html';
    die;
}

if($_GET['act'] === 'goods'){
    import::getMdl('products');
    import::getMdl('productKeywordMaps');
    $url = array();
    $searchWord = empty($_GET['word']) ? show404() : htmlspecialchars($_GET['word'],ENT_QUOTES,'UTF-8');
    $url['word'] = $searchWord; 
    $words = PSCWS5::getInstance()->getAllSplitCol($searchWord);
    if(empty($words)) show404 ();
    $words2 = html::wordsChangeColor($words);
    $keywordIds = keywordsMdl::getInstance()->getIdsByKeywords($words);
   
    if(!empty($keywordIds)){
        $t1 = microtime(true);
        $totalnum = productKeywordMapsMdl::getInstance()->getProductKeywordMapsCount($keywordIds);
        $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
        $pageMax = ceil($totalnum / PAGE_SIZE);
        $pageMax = $pageMax > 20 ? 20 : $pageMax;
        if ($_GET['page'] > $pageMax)
            $_GET['page'] = $pageMax;
        if ($_GET['page'] <= 0)
            $_GET['page'] = 1;
        $begin = ($_GET['page'] - 1) * PAGE_SIZE;
        $productsIds = productKeywordMapsMdl::getInstance()->getProductKeywordMapsList($keywordIds,$begin,PAGE_SIZE);
        $datas = productsMdl::getInstance()->getProductsByIds($productsIds);   
        $showDatas = array();
        foreach($datas as $val){
            $showDatas[$val['id']] = $val;
        }
        
        $links = createPage(mkUrl::linkTo('search','gooods',  array_merge($url,array('page'=>'%d'))), PAGE_SIZE, $_GET['page'], $totalnum);
        $showTime = round( microtime(true) - $t1 ,4);
       
    }
    $defaultSearch = 4;
    $_GET['ctl'] = 'mall';//改变导航的选中状态
    require TEMPLATE_PATH.'mall_search.html';
    die;
}