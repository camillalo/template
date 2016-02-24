<?php
if(!defined('INSTALL')) die('sorry');
$check_dir = array('data','data/cache','data/ueditor','data/upload','core/config');

function check_dir_write($path){
	if(!file_exists($path)){return false;}
	$file=$path.'write.txt';
	if(!$fp=fopen($file,'w')){return false;}
	if(!fwrite($fp,'write')){return false;}
	fclose($fp);
	unlink($file);
	return true;
}
require BASE_PATH.'install/template/check.html';
die;