<?php
/**
 * Created by PhpStorm.
 * User: a24
 * Date: 14-8-20
 * Time: 下午6:08
 */

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumei = new jumei();
    $jumei->cookie = $_POST['cookie'];
    $jumei->status = $_POST['status'];
    $data = $jumei->get_express();
    if($data) {
        echo '<pre>';
        print_r($data);
        exit;
    } else {
        echo '当前条件没有订单';
        exit;
    }


}

echo '<form method="post"><div>请提交从聚美优品header中获取的全部cookie</div><textarea name="cookie" style="width: 400px; height: 200px;" ></textarea><br/><br/>订单状态<br/><select name="status"><option value="4">最近全部订单</option><option value="2" selected>待收货</option></select><br/><br/><button>提交</button></form>';



class jumei
{
    public $cookie;
    public $status = 4;

    public function get_express()
    {
        $url = 'http://www.jumei.com/i/order/list/' . $this->status;
        $data = $this->curl($url);
        if($data) {
            // 1，解析商品名称，2，解析价格，3，解析下单时间，4，解析物流详情
            $ret = array();
            preg_match_all('/<p>订单编号：(.*)<\/p>/', $data, $orders);
            preg_match_all('/<p>下单时间：(.*)<\/p>/', $data, $buy_ats);
            preg_match_all('/<td\s+class="item_title">\n*\r*\s*<a\s+.*title="(.*)">\n*\r*\s*<img\s+src="(.*)"\s+alt/', $data,$titles);
            preg_match_all('/<p\s+class="bold">¥(.*)\s*<\/p>/', $data, $prices);
            preg_match_all('/<p>包裹 [(]\d+[)]<\/p>\r*\n*\s*<p>(.*)<\/p>\r*\n*\s*<p>(.*)<\/p>/', $data, $expresses);
            if(!empty($orders[1])) {
                foreach($orders[1] as $key => $val) {
                    $ret[] = array(
                        'order_id' => $val,
                        'buy_at' => isset($buy_ats[1][$key]) ? $buy_ats[1][$key] : '',
                        'title' => isset($titles[1][$key]) ? $titles[1][$key] : '',
                        'img_url' => isset($titles[2][$key]) ? $titles[2][$key] : '',
                        'price' => isset($prices[1][$key]) ? $prices[1][$key] : '',
                        'express_number' => isset($expresses[2][$key]) ? $expresses[2][$key] : '',
                        'brand' => isset($expresses[1][$key]) ? $expresses[1][$key] : ''
                    );
                }

                return $ret;
            }

            return array();
        }

        return array();
    }

    public function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.143 Safari/537.36');
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        $response = curl_exec($ch);
        curl_close($ch);

        if(!empty($response)) {
            return $response;
        }

        return '';
    }

    public function get_ip()
    {
        if (isset($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])){
            $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])){
            $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
        } elseif (isset($HTTP_SERVER_VARS["REMOTE_ADDR"])){
            $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")){
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")){
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = NULL;
        }
        return $ip;
    }
}