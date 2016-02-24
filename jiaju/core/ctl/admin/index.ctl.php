<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
session_write_close();
if($_GET['act'] === 'main'){

    require TEMPLATE_PATH.'index.html';
    die;
}


if($_GET['act'] === 'top'){
    $menu = import::getCfg('adminMenu');
    import::getMdl('group');
    $group = groupMdl::getInstance()->getGroup($_SESSION['admin']['group_id']);
    require TEMPLATE_PATH.'top.html';
    die;
}

if($_GET['act'] === 'left'){
    $menu = import::getCfg('adminMenu');
    $index = empty($_GET['index']) ? 0 : (int)$_GET['index'];
    $showMenu = isset($menu[$index]['item'])  ? $menu[$index]['item'] : array();
    $defaultUrl = isset($menu[$index]['link'])  ? $menu[$index]['link'] : 'index.php?act=default';
    require TEMPLATE_PATH.'left.html';
    die;
}

if($_GET['act'] === 'default'){
    import::getMdl('group');
    $group = groupMdl::getInstance()->getGroup($_SESSION['admin']['group_id']);
    require TEMPLATE_PATH.'default.html';
    die;
}

if($_GET['act'] === 'clear'){
    
    fileCache::getInstance()->flush();
    errorAlert('操作成功');
    die;
}