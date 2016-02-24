<?php
/**
 * Created by PhpStorm.
 * User: a24
 * Date: 14-8-18
 * Time: 下午1:07
 */

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jd = new jd();
    $jd->cookie = $_POST['cookie'];
    $jd->sid = $_POST['sid'];
    $jd->agent = $_POST['agent'];
    $status = $_POST['status'];
    $data = $jd->get_express($status);
    if($data) {
        echo '<pre>';
        print_r($data);
        exit;
    } else {
        echo '当前条件没有订单';
        exit;
    }


}

echo '<form method="post"><div>请提交从京东header中获取的全部cookie</div><textarea name="cookie" style="width: 400px; height: 200px;" ></textarea><br/><br/>京东登录后useragent：<br/><input type="text" name="agent" style="width: 400px;"><br/><br/>京东登录后url中的sid：<br/><input type="text" name="sid" style="width: 400px;"><br/><br/>订单状态<br/><select name="status"><option value="all">最近全部订单</option><option value="wait" selected>待收货</option></select><br/><br/><button>提交</button></form>';






class jd
{
    public $cookie;
    public $all_url = 'http://passport.m.jd.com/user/userAllOrderList.action?sid=';
    public $wait_url = 'http://passport.m.jd.com/user/waitDeliveryOrderList.action?sid=';
    public $sid;
    public $agent;

    // 获取运单信息
    public function get_express($status = 'wait')
    {
        $url = $status == 'wait' ? $this->wait_url . $this->sid : $this->all_url . $this->sid;
        $ret = $this->curl($url);

        if($ret) {
            if(!$this->check_cookie($ret)) {
                echo '<pre>';
                print_r('cookie已经失效!');
                exit;
            }
            // 解析数据
            // 1，订单号；2，购买时间；3，商品名称
            preg_match_all('/<a\s+class="new-mu_l2a\s+new-p-re"\s+href=\'(.*)\'>\r\n\s*<div\s+/', $ret, $link);
            preg_match_all('/<div\s+class="order-msg">\s*<p\s+class="title">\s+(.*)\s+<\/p>\r\n\s*<p\s+class="price">[&]yen[;](.*)<span><\/span><\/p>\r\n\s*<p\s+class="order-data">(.*)<\/p>/', $ret,$titles);

            if(!empty($link) && !empty($link[1])) {
                foreach($link[1] as $key => $val) {
                    // 匹配出url
                    preg_match('/orderId=(\d+)[&]/', $val, $url);
                    $data[] = array(
                        'order_id' => $url[1],
                        'title' => $titles[1][$key],
                        'buy_at' => $titles[3][$key],
                        'price' => $titles[2][$key]
                    );
                }

                return $data;
            }

            return array();
        } else {
            echo '获取数据失败，原因未知';
            exit;
        }
    }

    public function check_cookie($ret)
    {
        // 判断cookie状态
        if(!preg_match('/class="order-list"/', $ret)) {
            return false;
        };

        return true;
    }

    public function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
//        curl_setopt ($ch, CURLOPT_REFERER, 'https://passport.m.jd.com/user/allOrders.action?sid=df4bdd8015f4904ecfc4711fbaa56b3a');
        //curl_setopt( $ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
//
//        echo '<pre>';
//        print_r($response);
//        exit;

        if(!empty($response)) {
            return $response;
        }



        return '';
    }
}