<?php
if ( !defined ( 'BASE_PATH') )
{
	exit ( 'Access Denied' );
}
import::getInt('recommend');
recommend::getInstance()->init(1);
if($_GET['act'] === 'main'){
    import::getInt('seo');
    $__SETTING = seoInt::getInstance()->load('index');
    require TEMPLATE_PATH.'index.html';
    die;
}
