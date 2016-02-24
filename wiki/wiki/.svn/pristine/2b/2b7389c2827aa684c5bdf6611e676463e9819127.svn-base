<?php
/**
 * Created by PhpStorm.
 * User: a24
 * Date: 14-8-18
 * Time: 下午1:07
 */

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vip = new vip();
    $vip->cookie = $_POST['cookie'];
    $nos = $vip->get_order_numbers();

    $ret = array();
    foreach($nos as $no) {
        $detail = $vip->get_detail($no['order_id']);
        $express = $vip->get_express($no['order_id']);
        $ret[] = array_merge($detail, $express, $no);
    }
    echo '<pre>';
    print_r($ret);
    exit;

}

echo '<form method="post"><div>请提交从唯品会header中获取的全部cookie</div><textarea name="cookie" style="width: 400px; height: 200px;" ></textarea><br/><br/><button>提交</button></form>';





class vip
{
    public $cookie;
    // 获取订单号码
    public function get_order_numbers()
    {
        $url = 'http://m.vip.com/user-order-list-unreceive.html';
        $data = $this->curl($url);
        if($data) {
            preg_match_all('/<p>订单编号：(\d+)<\/p>/', $data, $mm);
            // <p class="fr sfontgrey">下单时间：2014-08-19 17:29:16</p>
            preg_match_all('/<p\s+class="fr\s+sfontgrey">下单时间：(.*)<\/p>/', $data, $time);
            if(!empty($mm[1])) {
                $ret = array();
                foreach($mm[1] as $key => $val) {
                    $ret[] = array(
                        'order_id' => $val,
                        'buy_at' => $time[1][$key]
                    );
                }

                return $ret;
            }
        }

        return array();
    }

    // 获取物流信息
    public function get_express($no)
    {
        $url = 'http://m.vip.com/index.php?m=user&act=order&step=logistics&order_sn=' . $no;
        $data = $this->curl($url);
        if($data) {
            preg_match('/<p>物流公司名：<span>(.*)<\/span><\/p>/', $data, $brand);
            preg_match('/<p>物流单号：<span\s+class="arial\s+fblod">(.*)<\/span><\/p>/', $data, $number);
            if(!empty($brand[1])) {
                return array(
                    'brand' => preg_replace('/[(].*[)]/', '', $brand[1]),
                    'express_number' => $number[1]
                );
            }
        }
    }

    // 获取订单详情信息
    public function get_detail($no)
    {
        $url = 'http://m.vip.com/index.php?m=user&act=order&step=detail&order_sn=' . $no;
        $data = $this->curl($url);
        if($data) {
            preg_match('/<p\s+class="cart_g_name">(.*)<\/p>/', $data, $title);
            preg_match('/<p\s+class="g_d_price">(.*)<\/p>/', $data, $price);
            if(!empty($title[1])) {
                return array(
                    'order_id' => $no,
                    'title' => $title[1],
                    'price' => $price[1]
                );
            }
        }
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

}