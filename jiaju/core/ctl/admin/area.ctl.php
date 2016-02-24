<?php

if (!defined('BASE_PATH')) {
    exit('Access Denied');
}
session_write_close();
define('PAGE_SIZE', 30); //分页大小
import::getMdl('area');
import::getInt('area');
if ($_GET['act'] === 'main') {
    $url = 'index.php?ctl=area&act=main';
    $_GET['keyword'] = empty($_GET['keyword']) ? '' : htmlspecialchars($_GET['keyword'], ENT_QUOTES, 'UTF-8');
    $where = array();
    if (!empty($_GET['keyword'])) {
        $url.='&keyword=' . urlencode($_GET['keyword']);
        $where['keyword'] = $_GET['keyword'];
    }

    
    $totalnum = areaMdl::getInstance()->getAreaCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int) $_GET['page'];
    $pageMax = ceil($totalnum / PAGE_SIZE);
    if ($_GET['page'] > $pageMax)
        $_GET['page'] = $pageMax;
    if ($_GET['page'] <= 0)
        $_GET['page'] = 1;
    $begin = ($_GET['page'] - 1) * PAGE_SIZE;

    $orderby = array('id' => 'DESC');
    $col = array('`id`', '`area_name`');
    $datas = areaMdl::getInstance()->getAreaList($col, $where, $orderby, $begin, PAGE_SIZE);
    $links = createPage($url . '&page=%d', PAGE_SIZE, $_GET['page'], $totalnum);
    logsInt::getInstance()->systemLogs('查看了区域列表');
    require TEMPLATE_PATH . 'area/main.html';
    die;
}

if ($_GET['act'] === 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
        $info['area_name'] = empty($_POST['area_name']) ? '' : trim(htmlspecialchars($_POST['area_name'], ENT_QUOTES, 'UTF-8'));
        if (empty($info['area_name']))
            errorAlert('区域名称不能为空');

        if (!areaMdl::getInstance()->addArea($info))
            errorAlert('操作失败');
        logsInt::getInstance()->systemLogs('新增了区域',$info,array());
        areaInt::getInstance()->put();
        echoJs("alert('操作成功');parent.location='index.php?ctl=area&act=add'");
        die;
    }
    require TEMPLATE_PATH . 'area/add.html';
    die;
}

if ($_GET['act'] === 'edit') {
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int) $_GET['id'];
    $data = areaMdl::getInstance()->getArea($id);
    if (empty($data))
        errorAlert('参数出错');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
        $info['area_name'] = empty($_POST['area_name']) ? '' : trim(htmlspecialchars($_POST['area_name'], ENT_QUOTES, 'UTF-8'));
        if (empty($info['area_name']))
            errorAlert('区域名称不能为空');

        if (false === areaMdl::getInstance()->updateArea($id, $info))
            errorAlert('操作失败');
        logsInt::getInstance()->systemLogs('编辑了区域',$data,$info);
        areaInt::getInstance()->put();
        echoJs("alert('操作成功');parent.location='index.php?ctl=area&act=edit&id=" . $id . "'");
        die;
    }
     logsInt::getInstance()->systemLogs('打开了区域的编辑模块');
    require TEMPLATE_PATH . 'area/edit.html';
    die;
}

if ($_GET['act'] === 'del') {
    $id = empty($_GET['id']) ? errorAlert('参数错误') : (int) $_GET['id'];
    $data = areaMdl::getInstance()->getArea($id);
    if (empty($data))
        errorAlert('参数出错');
    $back_url = empty($_GET['back_url']) ? 'index.php?ctl=area' : $_GET['back_url'];
    if (false !== areaMdl::getInstance()->delArea($id)) {
        logsInt::getInstance()->systemLogs('删除了区域',$data,array());
        areaInt::getInstance()->put();
        dieJs('alert("操作成功");parent.location="' . $back_url . '"');
    }
    errorAlert('操作失败');
    die;
}

