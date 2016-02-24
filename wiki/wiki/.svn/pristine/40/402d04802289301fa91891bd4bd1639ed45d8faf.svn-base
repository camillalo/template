<?php
/**
 * Created by PhpStorm.
 * User: chimero
 * Date: 14-6-7
 * Time: 21:44
 */

// 根目录
function baseUrl($uri = '') {
    return 'http://' . $_SERVER['HTTP_HOST'] . '/' . ltrim($uri, '/');
}
// 静态资源目录
function staticUrl($uri = '') {
    return baseUrl('static/' . ltrim($uri, '/'));
}

// 获取cookie
function getUserInfo($key = ''){
    $userinfo =  isset($_COOKIE['userinfo']) ?  unserialize($_COOKIE['userinfo']) : null;
    if($key && $userinfo && isset($userinfo[$key])) {
        return $userinfo[$key];
    }
    return $userinfo;
}

// 打印调试
function myprint() {
    $num = func_num_args();
    $arg = func_get_args();
    echo '<pre>';
    $num == 0 ? print_r(debug_backtrace()) : ($num ==1 ? print_r($arg[0]) : var_dump($arg[0]) );
    die;
}

/**
 * @param $data
 * @return string
 */
function toXml($data) {
    $xml = '';
    foreach($data as $key => $val) {
        is_numeric($key) && $key = 'item';
        $xml .= "<{$key}>";
        $xml .= is_array($val) ? toXml($val): htmlspecialchars($val);
        $xml .= "</{$key}>";
    }
    return $xml;
}

function arrayRecursive(&$array){
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key]);
        } else {
            if(is_string($value)){
                $temp1 = addslashes($value);
                $array[$key] = urlencode($temp1);
            }else{
                $array[$key] = $value;
            }
        }
    }
}

function jsonEncode($data) {
    if(!is_array($data)) {
        return false;
    }
    $array = $data;
    arrayRecursive($array);
    $json = json_encode($array);
    return urldecode($json);
}

// 输出到html
function prettyOut($arr = '', $format = 'json') {
    if(empty($arr)) {
        return 'null';
    }
    switch($format) {
        case 'json':
            return jsonFormat($arr) ? jsonFormat($arr) : $arr;
            break;
        case 'array':
            return print_r($arr, true);
            break;
        case 'phparray':
            return var_export($arr, true);
            break;
        case 'xml':
            $response = array();
            $response['response']['header'] = array('format' => 'xml');
            if(isset($arr['response']['header'])) {
                $response['response']['header'] = array_merge($arr['response']['header'], $response['response']['header']);
            }
            $response['response']['body'] = isset($arr['response']['body']) ? $arr['response']['body'] : $arr;
            return htmlentities('<?xml version="1.0" encoding="UTF-8"?>'.toXml($response), ENT_QUOTES, 'utf-8', FALSE);
            break;
    }

}


function jsonFormat($data, $indent=null){

    // 对数组中每个元素递归进行urlencode操作，保护中文字符
    // 查看本栏目更多精彩内容：http://www.bianceng.cn/webkf/PHP/
    array_walk_recursive($data, 'jsonFormatProtect');

    // json encode
    $data = json_encode($data);

    // 将urlencode的内容进行urldecode
    $data = urldecode($data);

    // 缩进处理
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent)? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;

    for($i=0; $i<=$length; $i++){

        $char = substr($data, $i, 1);

        if($char=='"' && $prevchar!='\\'){
            $outofquotes = !$outofquotes;
        }elseif(($char=='}' || $char==']') && $outofquotes){
            $ret .= $newline;
            $pos --;
            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }

        $ret .= $char;

        if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
            $ret .= $newline;
            if($char=='{' || $char=='['){
                $pos ++;
            }

            for($j=0; $j<$pos; $j++){
                $ret .= $indent;
            }
        }

        $prevchar = $char;
    }

    return $ret;
}

function jsonFormatProtect(&$val){
    if($val!==true && $val!==false && $val!==null){
        $val = urlencode($val);
    }
}
function send_post($url, $post_data=array()) {
	$postdata = http_build_query($post_data);
	$options = array(
			'http' => array(
					'method' => 'POST',
					'header' => 'Content-type:application/x-www-form-urlencoded',
					'content' => $postdata,
					'timeout' => 15 * 60 // 超时时间（单位:s）
			)
	);
	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	return $result;
}


function post($url, $cookie = '', $post = null) {
    $context = array();
    if (is_array($post)) {
        $context['http'] = array (
            'timeout'=> 5,
            'method' => 'POST',
            'header' => "Cookie: session_id=" . $cookie,//http请求时带上cookie值
            'content' => http_build_query($post, '', '&'),
        );
    }
    return @file_get_contents($url, false, stream_context_create($context));
}
