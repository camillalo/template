<?php
/**
 ***************************************
 * 淘宝订单测试页面
 * @author llq
 ****************************************
 */

if($_SERVER['REQUEST_METHOD'] == 'POST') {


    $cookies = $_POST['cookie'];
    if(empty($cookies)) {
        echo '<script>alert("请输入cookie！");window.location.href = "/taobao.php";</script>';
    }


    $taobao = new taobao($cookies);


    echo '+++++++++++++++当前登录用户信息+++++++++++++++<pre>';
    //print_r($taobao->get_user_info());



    $ret = array();
    $result = $taobao->get_order_list($_POST['status']);

    $ex = array();
    // 取出单号
    $order_numbers = $taobao->get_order_numbers($result['data']);
    foreach($order_numbers as $key => $val) {
        $express = $taobao->get_order_express($key);
        $ex[] = $express;
        $express_numbers = $taobao->get_express_numbers($express['data']);
        if(!empty($express_numbers)) {
            $ret[] = array(
                'express' => $express_numbers,
                'note' => $val['note'],
                'order_id' => $key
            );
        }
    }
    echo '+++++++++++++++当前登录用户最近24条快递单号列表+++++++++++++++<pre>';
    print_r($ret);
    echo '+++++++++++++++当前登录用户最近订单数据展示+++++++++++++++<pre>';
    print_r($result);
    echo '+++++++++++++++当前登录用户最近物流数据展示+++++++++++++++<pre>';
    print_r($ex);
    exit;
}

echo '<form method="post"><div>请提交从m淘宝header中获取的全部cookie</div></div><textarea name="cookie" style="width: 400px; height: 200px;" ></textarea><br><select name="status"><option value="4">状态4（可能是全部已经完成订单）</option><option value="5">状态5（可能是物流派送中订单）</option> <option value="3">状态3（未知状态）</option><option value="2">状态2（未知状态）</option><option value="1">状态1（未知状态）</option><option value="0">状态0（未知状态）</option></select><button>提交</button></form>';



/**
 * Class taobao
 */
class taobao
{
    public $token;
    public $time;
    public $cookies;
    public $sign;

    // 初始化
    public function __construct($cookies = '')
    {
        $this->set_cookie($cookies);
    }

    // 设置cookie
    public function set_cookie($cookies = '')
    {
        if(!empty($cookies)) {
            $this->cookies = $cookies;
            $this->fetch_cookie();
        }
    }

    // 解析cookie
    public function fetch_cookie()
    {
        preg_match('/_m_h5_tk=(.*);\s*_m/', $this->cookies, $mh);
        if(isset($mh[1]) && $mh[1]) {
            $ori = explode('_', $mh[1]);
            $this->token = $ori[0];
            $this->time = $ori[1];
        }
    }

    // 刷新cookie
    public function refresh_cookie() {}

    /**
     * 生成签名
     * @param $token
     * @param $time
     * @param $data
     */
    public function create_sign(array $data = array())
    {
        $json =  empty($data) ? '{}' : json_encode($data);
        return $this->sign = md5($this->token . '&' . $this->time . '&12574478&' . $json);
    }

    /**
     ***********************************************************
     * 可用APIS
     * Date: 14-7-21
     ***********************************************************
     */
    /**
     * 获取当前登录用户信息
     */
    public function get_user_info()
    {
        $query = array(
            'callback' => 'jsonp1',
            'type' => 'jsonp',
            'api' => 'com.taobao.client.user.getUserInfo',
            'data' => '{}',
            'v' => '1.0',
            'appKey' => 12574478,
            't' => $this->time,
            'sign' => $this->create_sign()
        );
        return $this->curl($this->get_user_info_url($query));
    }

    // 获取用户基本信息url
    public function get_user_info_url($query)
    {
        return 'http://api.m.taobao.com/rest/h5ApiUpdate.do?' . http_build_query($query);
    }

    // 获取当前当用户订单列表
    public function get_order_list($status = 4, $page = 1, $page_size = 24, $keywords = '')
    {
        $data = array(
            'statusId' => $status,
            'page' => $page,
            'pageSize' => $page_size,
            'q' => $keywords
        );
        $query = array(
            'callback' => 'jsonp1',
            'type' => 'jsonp',
            'sprefer' => 'p23590',
            'api' => 'mtop.order.queryOrderList',
            'data' => json_encode($data),
            'v' => '1.0',
            'appKey' => 12574478,
            't' => $this->time,
            'sign' => $this->create_sign($data)
        );
        return $this->curl($this->get_order_list_url($query));
    }

    // 获取用户订单列表url
    public function get_order_list_url($query)
    {
        return 'http://api.m.taobao.com/rest/h5ApiUpdate.do?' . http_build_query($query);
    }

    // 获取可用id列表
    public function get_order_numbers($data)
    {
        $orders = array();
        if(isset($data['total']) && $data['total']) {
            foreach($data['cell'] as $val) {
                if($val['bizType'] == 200) {
                    $orders[$val['orderId']] = array(
                        'order_number' => $val['orderId'],
                        'note' => $val['sellerNick']
                    );
                }
            }
        }
        return $orders;
    }

    // 获取运单号码
    public function get_express_numbers($data)
    {
        $express = array();
        if(isset($data['orderList']) && !empty($data['orderList'])) {
            foreach($data['orderList'] as $val) {
                foreach($val['bagList'] as $val2) {
                    if(isset($val2['mailNo']) && $val2['mailNo']) {
                        $express[] = array(
                            'express_number' => $val2['mailNo'],
                            'brand' => $val2['partnerName']
                        );
                    }
                }
            }
        }
        return $express;
    }

    // 获取订单物流信息
    public function get_order_express($order_id = '')
    {
        $data = array(
            'orderId' => $order_id
        );
        $query = array(
            'callback' => 'jsonp1',
            'type' => 'jsonp',
            'sprefer' => 'p23590',
            'api' => 'mtop.logistic.getLogisticByOrderId',
            'data' => json_encode($data),
            'v' => '1.0',
            'appKey' => 12574478,
            't' => $this->time,
            'sign' => $this->create_sign($data)
        );
        return $this->curl($this->get_order_express_url($query));
    }

    // 获取运单列表url
    public function get_order_express_url($query)
    {
        return 'http://api.m.taobao.com/rest/h5ApiUpdate.do?' . http_build_query($query);
    }


    /**
     ***********************************************************
     * curl
     ***********************************************************
     */
    protected  function curl($url, $ip = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.5)
        Gecko/20091102 Firefox/3.5.5');
        curl_setopt ($ch, CURLOPT_REFERER, 'http://h5.m.taobao.com/awp/mtb/mtb.htm?spm=0.0.0.0&sprefer=p23590');
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookies);
        if(!empty($ip)) {
            // 伪造请求ip
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        preg_match('/^jsonp1[(]({.*})[)]$/', trim($response), $matches);


        echo '<pre>';
        print_r($response);
        exit;
        // 处理返回数据情况
        // 1)IP被封掉
        $response = preg_replace('/\s+/', '', $response);
        if(empty($response)) {
            return array(
                'code' => 1000,
                'msg' => '服务器端IP已经被封，请APP端自行处理数据！',
                'data' => array()
            );
        }

        // 3)接口调用成功
        if($response && !empty($matches) && isset($matches[1]) && $matches[1]) {
            $result = json_decode($matches[1], true);
            if(preg_match('/SUCCESS/', $result['ret'][0])) {
                return array(
                    'code' => 0,
                    'msg' => '成功！',
                    'data' => $result['data']
                );
            }
        }
//        echo '<pre>';
//        print_r($response);
//        exit;
        // 3)COOKIE或者session失效
        return array(
            'code' => 1,
            'msg' => 'Cookie已经失效，请重新获取！',
            'data' => array()
        );
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


