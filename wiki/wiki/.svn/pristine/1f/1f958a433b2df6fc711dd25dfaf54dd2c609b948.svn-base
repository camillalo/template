<?php

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pr = isset($_POST['pr']) && $_POST['pr'] ? $_POST['pr'] : 5;
    $lat = isset($_POST['lat']) && $_POST['lat'] ? $_POST['lat'] : 0;
    $lng = isset($_POST['lng']) && $_POST['lng'] ? $_POST['lng'] : 0;

// 检测参数
    if(empty($lat) || empty($lng)) {
        echo '参数错误！';
        exit;
    }
    $query = array(
        'act' => 'nearby',
        'pr' => $pr,
        'lat' => $lat,
        'lng' => $lng,
    );
    $map_url = "http://shop2.interface.kuaidihelp.com/map/?" . http_build_query($query);
    $result = @json_decode(file_get_contents($map_url), true);
    echo '<pre>';
    print_r($result);
    exit;
}


echo '<form method="post">经度：<br><input type="text" name="lng" value="121.36134773257"/><br><br>纬度：<br><input type="text" name="lat" value="31.229840918589"/><br><br><select name="pr"><option value="8">100米内</option><option value="7">350米内</option><option value="6">3公里内</option><option value="5">10公里内</option> <option value="4">60公里内</option></select><br><br><button>提交</button></form><br/><br/>哦，默认经纬度为公司地址';
