<?php
if ( !defined ( 'BASE_PATH') )
{
    exit ( 'Access Denied' );
}


function getTemplateArr(){
    $dir = BASE_PATH .'themes/';
    $mulu = scandir($dir);
    $array = array('.','..','admin','mail','company');
    return array_diff($mulu, $array);
}

//获取 登录后的UID 
function getUid(){ 
   $login_info = getCk('login_info');
   if(empty($login_info)) return null;
   list($uid,$t,$ip) = explode('|',$login_info);
   if($t < NOWTIME - 86400)  $uid = null;
   if($ip != getIp()) $uid = null;
   return (int) $uid;
}

//生成一个 CONFIG 文件
function makeCfg($key,$val){
    
    $filename = BASE_PATH.'core/config/'.$key.'.cfg.php';
    
    $str = '<?php return '.  var_export($val,true).';?>';
    
    return file_put_contents($filename, $str);
}

//后台判断权限的时候显示
function noAccess(){
    
    require TEMPLATE_PATH.'noAccess.html';
    die;
}
function show404($back_url='/'){
    require BASE_PATH.'/statics/images/404.html';
    die;
}

function getDomain(){
    $ret = strpos($_SERVER['HTTP_HOST'],'.');
    if($ret === false) return $_SERVER['HTTP_HOST']; 
    return  substr(SITE_URL,strpos(SITE_URL, '.') + 1);
}

//存入一个值到COOKIE 里面 因为全国性的站点那么不考虑在什么域名下的二级站点了
function setCk($key,$val,$time=3600){
    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
    if(!empty($val)) $val = authcode($val);
    return setcookie ($key,$val, NOWTIME+$time);
}
//取得COOKIE
function getCk($key){
    return isset($_COOKIE[$key]) ? authcode($_COOKIE[$key],'DECODE') : null;   
}

//输出错误并中断操操作 主要用于异步用
function dieJsonErr($message){
    die(json_encode(array('ret'=>-1,'message'=>$message)));
}
//输出正确的结果 主要用于异步
function dieJsonRight($message){
    //写入LOG
    die(json_encode(array('ret'=> 0 ,'message'=>$message)));
    
}
//如果有GET的数据需要在模版中显示就用这个转义一下
function addslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('addslashes_deep', $value) :
                addslashes($value);

    return $value;
}
//如果不需要转义 而被转义的用这个返转一下
function stripslashes_deep($value)
{
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

    return $value;
}




/*
 * 打印测试
 */
function da($arr_str, $exit = false)
{
    echo "<pre>";
    print_r($arr_str);
    echo "</pre>";
    ($exit)?exit:'';
}

/*
 * 加密解密函数
 */
function authcode($string, $operation = "ENCODE") {
    $key = AUTH_KEY;
    $key_length = strlen($key);

    $string = $operation == 'DECODE' ? base64_decode($string) : substr(md5($string.$key), 0, 8).$string;
    $string_length = strlen($string);

    $rndkey = $box = array();
    $result = '';

    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($key[$i % $key_length]);
        $box[$i] = $i;
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if(substr($result, 0, 8) == substr(md5(substr($result, 8).$key), 0, 8)) {
            return substr($result, 8);
        } else {
            return '';
        }
    } else {
        return str_replace('=', '', base64_encode($result));
    }

}


/*
 * 取随机数
 * @param int $length int $type
 */
function random($length=6,$type=2){
    $hash = '';
    $chararr =array(
        'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz',
        '0123456789',
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    );
    $chars=$chararr[$type];
    $max = strlen($chars) - 1;
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/*
 * 获取真实IP
 */
function getIp()  {
    if(!empty($_SERVER["HTTP_CLIENT_IP"]))$cip = $_SERVER["HTTP_CLIENT_IP"];
    else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))     $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    else if(!empty($_SERVER["REMOTE_ADDR"]))     $cip = $_SERVER["REMOTE_ADDR"];
    else     $cip = null;  return $cip;
}


/*
 * 过滤 JS  等不安全代码
 * @param string $str
 */
function getValue($str , $ty = false){
    $str = preg_replace( "@<script(.*?)</script>@is", "", $str );
    $str = preg_replace( "@<iframe(.*?)</iframe>@is", "", $str );
    $str = preg_replace( "@<style(.*?)</style>@is", "", $str );
    while(preg_match('/(<[^><]+)( lang|action|codebase|dynsrc|lowsrc
                                    |onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate
                                    |onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus
                                    |onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur
                                    |onbounce|oncellchange|onchange|onclick|oncontextmenu|
                                     oncontrolselect|oncopy|oncut|ondataavaible|ondatasetchanged|
                                    ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragdrop|
                                     ondragend|ondragenter|ondragleave|ondragover|ondragstart|
                                     ondrop|onerror|onerrorupdate|onfilterupdate|onfinish|onfocus|
                                     onfocusin|onfocusout|onhelp|onkeydown|onkeypress|onkeyup|
                                     onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|
                                     onmouseleave|onmousemove|onmoveout|onmouseover|onmouseup|
                                     onmousewheel|onmove|onmoveend|onmovestart|onpaste|
                                     onpropertychange|onreadystatechange|onreset|onresize|onresizeend|
                                     onresizestart|onrowexit|onrowsdelete|onrowsinserted|onscroll|
                                     onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|
                                     onunload)[^><]+/i',$str,$mat)){
        $str=str_replace($mat[0],$mat[1],$str);
    }
    if($ty){
        $str = preg_replace( "@<(.*?)>@is", "", $str );
    }
    return $str;
}


/**
 * 判断一个字符串是否是一个Email地址
 *
 * @param string $string
 * @return boolean
 */
function isEmail($string)
{
    return (boolean) preg_match('/^[a-z0-9.\-_]{2,64}@[a-z0-9]{2,32}(\.[a-z0-9]{2,5})+$/i', $string);
}


/**
 * 检查是否为一个合法的时间格式
 *
 * @access  public
 * @param   string  $time
 * @return  void
 */
function isTime($time)
{
    $pattern = '/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/';

    return preg_match($pattern, $time);
}

/**
 * 判断一个字符串是否是一个合法时间
 *
 * @param string $string
 * @return boolean
 */
function isDate($string)
{
    if (preg_match('/^\d{4}-[0-9][0-9]-[0-9][0-9]$/', $string))
    {
        $date_info  = explode('-', $string);
        return checkdate(ltrim($date_info[1], '0'), ltrim($date_info[2], '0'), $date_info[0]);
    }
    if (preg_match('/^\d{8}$/', $string))
    {
        return checkdate(ltrim(substr($string, 4, 2), '0'), ltrim(substr($string, 6, 2), '0'), substr($string, 0, 4));
    }
    return false;
}

/**
 * 判断输入的字符串是否是一个合法的电话号码（仅限中国大陆）
 *
 * @param string $string
 * @return boolean
 */
function isPhone($string)
{
    if (preg_match('/^0\d{2,3}-\d{7,8}$/', $string))   return true;
    return false;
}

/**
 * 判断输入的字符串是否是一个合法的手机号(仅限中国大陆)
 *
 * @param string $string
 * @return boolean
 */
function isMobile($string)
{
    return ctype_digit($string) && (11 == strlen($string)) && ($string[0] == 1);
}
/**
 * 判断一个字符传是否是中文字符
 *
 * @param string $string
 * @return boolean
 */
function isChinese($string)
{
    return (boolean) preg_match('/^[\x80-\xff]+$/', $string);
}

/**
 * 判断输入的字符串是否是一个合法的QQ
 *
 * @param string $string
 * @return boolean
 */
function isQQ($string)
{
    if(ctype_digit($string))
    {
        $len	= strlen($string);
        if($len < 5 || $len > 13) return false;
        return true;
    }
    return isEmail($string);
}

/**
 * 判断一个字符串是否合法的邮编
 *
 * @param string $string
 * @return boolean
 */
function isZip($string)
{
    return strlen($string) === 6 && ctype_digit($string);
}
/**
 *
 * @param string $fileName
 * @return boolean
 */
function isImage($fileName)
{
    $ext    = explode('.', $fileName);
    $ext_seg_num    = count($ext);
    if ($ext_seg_num <= 1)  return false;

    $ext    = strtolower($ext[$ext_seg_num - 1]);
    return in_array($ext, array('jpeg', 'jpg', 'png', 'gif'));
}


/**
 * 分页函数
 *
 * @param string $url
 * @param int $perPage
 * @param int $currentPage
 * @param int $totalItems
 * @param int $delta
 * @param string $target
 * @return string
 */
function createPage($url, $perPage, $currentPage, $totalItems, $delta = 2, $target = '_self')
{
    $t_high = ceil($totalItems / $perPage);    
    $high = $currentPage + $delta;
    $low = $currentPage - $delta;
    if ($high > $t_high)	{
        $high = $t_high;
        $low = $t_high - 2 * $delta;
    }
    if ($low < 1) {
        $low = 1;
        $high = $low + 2 * $delta;
        if($high > $t_high) $high = $t_high;
    }
    $offset = ($currentPage - 1) * $perPage + 1;
    if ($offset < 0) $offset = 0;
    $end = $offset + $perPage - 1;
    if($end > $totalItems) $end = $totalItems;
    $ret_string = "<ul class='multipage'><li>共({$totalItems})条</li><li>当前显示{$offset}-{$end}条</li>";
    if($currentPage > 1)
    {
        $ret_string .= "<li class='link'><a href='" . str_replace('%d', 1, $url) . "' target='{$target}' class='multipage'>首页</a></li>";
        $ret_string .= "<li class='link'><a href='" . str_replace('%d', $currentPage - 1, $url) . "' class='multipage' target='{$target}'>前一页</a></li>";
    }
    else {
        $ret_string .= "<li><span class='no_page'>首页</span></li>";
        $ret_string .= "<li><span class='no_page'>前一页</span></li>";
    }
    $links = array();
    for (;$low <= $high; $low++)
    {
        if($low != $currentPage) $links[] = '<li class=\'link\'><a href=\'' . str_replace('%d', $low, $url) . '\' class=\'multipage\' target=\'' . $target . '\'>' . $low . '</a></li>';
        else $links[] = "<li class='current'><span class='current_page'>{$low}</span></li>";
    }
    $links = implode('', $links);
    $ret_string .= ' ' . $links;
    if($currentPage < $t_high){
        $ret_string .= "<li class='link'><a href='" . str_replace('%d', $currentPage + 1, $url) . "' class='multipage' target='{$target}'>后一页</a></li>";
        $ret_string .= '<li class=\'link\'><a href=\'' . str_replace('%d', $t_high, $url) . '\' class=\'multipage\' target=\'' . $target . '\'>尾页</a>';
    }
    else{
        $ret_string .= "<li><span class='no_page'>后一页</span></li>";
        $ret_string .= "<li><span class='no_page'>尾页</span></li>";
    }
    return $ret_string . '</ul>';
}

/**
 * 快速调用XML-RPC
 *
 * @param string $server_addr
 * @param string $method
 * @param array $parameters
 * @param string $charset
 * @return mixed 如果返回值为 -9999, 则表示RPC错误
 */
function fast_rpc_call($server_addr, $method, Array $parameters, $charset = 'GBK')
{
    $request    = xmlrpc_encode_request($method, $parameters, array(    'escaping'  => 'markup',
                                                                            'encoding'  => $charset));
    $context    = stream_context_create(array(	'http'	=> array(   'method'    => 'POST',
                                                                        'header'	=> 'Content-Type: text/xml',
                                                                        'content'	=> $request)));
    $response   = file_get_contents($server_addr, null, $context);

    $response   = xmlrpc_decode($response);
    if(is_array($response))
    {
        if(xmlrpc_is_fault($response))
        {
            throw new Exception($response);
        }
    }
    return $response;
}



/**
 * 在客户端alert一条消息之后并且终止
 *
 * @param string $message
 */
function errorAlert($message)
{   
    import::getInt('injectionInfo');
    injectionInfo::getInstance()->setNotRecord();
    echoJs("alert('{$message}');");
    die;
}

/**
 * 向客户端发送一段Javascript消息
 *
 * @param string $message
 */
function echoJs($message)
{
        
        echo <<<EOF
        <script type='text/javascript'>
        {$message}
        </script>
EOF;
}

/**
 * 向客户端发送一段Js之后终止
 *
 * @param string $message
 */
function dieJs($message)
{
    echoJs($message);
    die;
}



/*GBK 转UTF-8*/
function gbkToUtf8($a){
    if(is_array($a)){
        $b=array();
        foreach($a as $k=>$v){
            $b[$k]=gbkToUtf8($v);
        }
    }else{
        $b=mb_convert_encoding($a,"utf-8","gbk");
    }
    return $b;
}

function utf8ToGbk($a){
    if(is_array($a)){
        $b=array();
        foreach($a as $k=>$v){
            $b[$k]=utf8ToGbk($v);
        }
    }else{
        $b=mb_convert_encoding($a,"gbk","utf-8");
    }
    return $b;
}


