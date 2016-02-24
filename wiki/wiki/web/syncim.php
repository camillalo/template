<?php
function post($url, $post = null){
    $context = array();
    if (is_array($post)) {
        $context['http'] = array (
            'timeout'=> 50,
            'method' => 'POST',
            'header' => "Cookie:session_id=s7d05ad3131455e60d3ef642d1e5a99e0",//http请求时带上cookie值
            'content' => http_build_query($post, '', '&'),
        );
    }
    return @file_get_contents($url, false, stream_context_create($context));
}

$url = 'http://dts.kuaidihelp.com/api.php';//请求地址
$key = 'bac500a42230c8d7d1820f1f1fa9b578'; //密钥
$order_numbers = array(
    '141015545652511',
    '141024371955485',
    '141022741277251',
    '141024937273412'
);
$data = array(
    'pname' => 'androids',
    'sname' => 'order.sync.im',
    'last_sync_time' => date('Y-m-d H:i:s', strtotime('-10 day')),
    //'user_role' => 2,
    'order_numbers' => json_encode($order_numbers)
);
$request = array(
    'content' => json_encode($data),//请求内容
    'token'   => md5(json_encode($data).$key),//token值
);
$start = microtime(true);
$result = post($url, $request);//执行post请求

echo '<pre>';
print_r(json_decode($result, true));
echo (microtime(true) - $start);
exit;

