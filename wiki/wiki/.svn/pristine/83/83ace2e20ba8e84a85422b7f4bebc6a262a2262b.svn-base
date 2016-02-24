<?php
// 按照次数解密base64
function decode64($str, $times = 1) {
    for($i = 1; $i<= $times; $i++) {
        $str = base64_decode($str);
    }
    return $str;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $url = $_POST['url'];
    $content = file_get_contents($url);
    preg_match('/var\s+g_sld="(.*)"\s*[;]\s+var/', $content, $m1);
    preg_match('/[|]g_ssm[|][|](\d)[|]g_sld[|]/', $content, $m2);

    // 邮件中标记红色的2，第一步解密次数
    $times = $m2[1];
    // 原始字符串
    $ori_str = $m1[1];
    // 第一步获取到的字符串
    $first_str = substr(decode64($ori_str, $times), $times*2);
    // 最后获取到的字符串
    $last_str = decode64($first_str);

    echo $last_str;
    exit;
}

$url = "http://qz.yundasys.com:18090/wsd/kjcx/cxend.jsp?wen=59423c58e14e9c519403320cea&jmm=b4b310b4238726671069ac703316e26c";
echo '提示：请填写url，如：' . $url;
echo '<br/><form method="post" action=""><input name="url" style="width: 300px;"> <button>提交url</button></form>';

?>

