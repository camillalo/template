<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}


$tools_array = array(
 'main' => '涂料精确预算器',
 'qiangzhuan' => '装修墙砖精确预算器',
 'dizhuan' => '地砖计算器',
 'chuanlian' => '窗帘计算器',
 'bizhi' => '壁纸计算器',
 'diban' => '地板计算器',
 'anjie' => '按揭贷款计算器',
'denge' => '等额本金还贷计算器'
);


if($_GET['act'] === 'main'){
	
	$title = "涂料精确预算器";
    require TEMPLATE_PATH.'tools/main.html';
    die;
}

if($_GET['act'] === 'qiangzhuan'){
	
	$title = "装修墙砖精确预算器";
    require TEMPLATE_PATH.'tools/qiangzhuan.html';
    die;
}

if($_GET['act'] === 'dizhuan'){
	
	$title = "地砖计算器";
    require TEMPLATE_PATH.'tools/dizhuan.html';
    die;
}
if($_GET['act'] === 'chuanlian'){
	
	$title = "窗帘计算器";
    require TEMPLATE_PATH.'tools/chuanlian.html';
    die;
}
if($_GET['act'] === 'bizhi'){
	
	$title = "壁纸计算器";
    require TEMPLATE_PATH.'tools/bizhi.html';
    die;
}
if($_GET['act'] === 'diban'){
	
	$title = "地板计算器";
    require TEMPLATE_PATH.'tools/diban.html';
    die;
}
if($_GET['act'] === 'anjie'){
	
	$title = "按揭贷款计算器";
    require TEMPLATE_PATH.'tools/anjie.html';
    die;
}
if($_GET['act'] === 'denge'){
	
	$title = "等额本金还贷计算器";
    require TEMPLATE_PATH.'tools/denge.html';
    die;
}

