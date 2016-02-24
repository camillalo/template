<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}
define('PAGE_SIZE',10);//分页大小
import::getMdl('team');
if($_GET['act'] === 'main'){

    $where  = array();

    $totalnum = teamMdl::getInstance()->getTeamCount($where);
    $_GET['page'] = empty($_GET['page']) ? 1 : (int)$_GET['page']; 
    $pageMax = ceil($totalnum/PAGE_SIZE);    
    if($_GET['page'] > $pageMax) $_GET['page'] = $pageMax;
    if($_GET['page']<=0) $_GET['page'] = 1;   
    $begin = ($_GET['page'] -1) * PAGE_SIZE;
    
    $orderby = array('orderby'=>'desc','id'=>'DESC');   
    
    $datas = teamMdl::getInstance()->getTeamList(array(),$where,$orderby,$begin,PAGE_SIZE);
    $links = createPage(mkUrl::linkTo('team','main',array('page'=>'%d')), PAGE_SIZE, $_GET['page'], $totalnum);
    require TEMPLATE_PATH.'team.html';
    die;
}
