<?php
/**
 ***********************************************************************************************************************
 * HTTP请求工具类
 ***********************************************************************************************************************
 */
/**
 * Class http
 * @package artisan
 */
class http
{
    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public static function get($url, $data = array(), $options = array())
    {
        if(empty($data)) {
            return self::curl($url, array(), $options);
        }
        // 拼接参数到url中
        $url = preg_match('/[?]/', $url) ? rtrim($url, '&') . '&' . http_build_query($data) : rtrim($url, '?') . '?' . http_build_query($data);
        return self::curl($url, array(), $options);
    }


    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public static function post($url, $data = array(), $options = array())
    {
        return self::curl($url, $data, $options);
    }


    /**
     * @param $url
     * @param array $data
     * @param array $options
     * @return mixed|null
     */
    public static function curl($url, $data = array(), $options = array())
    {
        if(empty($url)) {
            return null;
        }

        // 自动添加HTTP
        if(!preg_match('/^http[s]?[:]\/\/(.*)/', $url)) {
            $url = 'http://' . $url;
        }

        // 处理$options
        if(!empty($options)) {
            foreach($options as $key => $option) {
                $key = preg_replace('/(CURLOPT_|curlopt_)/', '', $key);
                unset($options[$key]);
                $options[strtoupper($key)] = $option;
            }
        }

        // 初始化
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // 设置HTTPHEADER
        if(!empty($options['HTTPHEADER'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['HTTPHEADER']);
        }

        // 设置超时时间 默认5秒
        $timeout = !empty($options['TIMEOUT']) ? intval($options['TIMEOUT']) : 5;
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        // 设置只解析IPV4
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 处理dns秒级信号丢失问题
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);

        // 模拟浏览器标识
        //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36');

        // 设置COOKIE
        if(!empty($options['COOKIE'])) {
            curl_setopt($ch, CURLOPT_COOKIE, $options['COOKIE']);
        }

        // POST请求
        // 注意：这里默认GET请求的参数附带在URL里，如果直接使用http::curl()方法，并且传data参数，会触发POST请求
        if(!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        // 设置头部
        if(!empty($options['HEADER'])) {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }

        // 设置其他参数
        $dcs = get_defined_constants(true);
        foreach($options as $option => $val) {
            if(!in_array($option, array('HEADER', 'COOKIE', 'TIMEOUT', 'HTTPHEADER'))) {
                $opt = 'CURLOPT_' . $option;
                $opt_defined = isset($dcs['curl'][$opt]) ? $dcs['curl'][$opt] : 0;
                if($opt_defined != 0) {
                    curl_setopt($ch, $opt_defined, $val);
                }
            }
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    /**
     * 批量发GET送请求
     * @param $urls
     * @param array $options
     * @param null $callback
     * @return array
     */
    public static function mutiGet($urls, $options = array(), $callback = null)
    {
        // 组织数据
        foreach((array)$urls as $key => $url) {
            if(is_string($url)) {
                $urls[$key] = array(
                    'url' => $url,
                    'data' => null
                );
            }
        }
        return self::mutiCurl($urls, $options, $callback);
    }


    /**
     * 批量发POST送请求
     * @param $requests
     * @param array $options
     * @param null $callback
     * @return array
     */
    public static function mutiPost($requests, $options = array(), $callback = null)
    {
        return self::mutiCurl($requests, $options, $callback);
    }


    /**
     * @param $requests
     * @param array $options
     * @param null $callback
     * @param int $delay
     * @return array|null
     */
    public static function mutiCurl($requests, $options = array(), $callback = null, $delay = 50)
    {
        if(empty($requests)) {
            return null;
        }

        // 处理$options
        if(!empty($options)) {
            foreach($options as $key => $option) {
                $key = preg_replace('/(CURLOPT_|curlopt_)/', '', $key);
                unset($options[$key]);
                $options[strtoupper($key)] = $option;
            }
        }

        $queue = curl_multi_init();
        $map = array();
        foreach ($requests as $id => $request) {
            $ch = curl_init();

            // 自动添加HTTP
            if(!preg_match('/^http[:]\/\/(.*)/', $request['url'])) {
                $request['url'] = 'http://' . $request['url'];
            }

            curl_setopt($ch, CURLOPT_URL, $request['url']);


            // 设置HTTPHEADER
            if(!empty($options['HTTPHEADER'])) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $options['HTTPHEADER']);
            }

            // 设置超时时间 默认5秒
            $timeout = !empty($options['TIMEOUT']) ? intval($options['TIMEOUT']) : 5;
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

            // 设置只解析IPV4
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36');

            // 设置COOKIE
            if(!empty($options['COOKIE'])) {
                curl_setopt($ch, CURLOPT_COOKIE, $options['COOKIE']);
            }

            // POST请求
            if(!empty($request['data'])) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request['data']);
            }

            // 设置头部
            if(!empty($options['HEADER'])) {
                curl_setopt($ch, CURLOPT_HEADER, true);
            }

            // 设置其他参数
            $dcs = get_defined_constants(true);
            foreach($options as $option => $val) {
                if(!in_array($option, array('HEADER', 'COOKIE', 'TIMEOUT', 'HTTPHEADER'))) {
                    $opt = 'CURLOPT_' . $option;
                    $opt_defined = isset($dcs['curl'][$opt]) ? $dcs['curl'][$opt] : 0;
                    if($opt_defined != 0) {
                        curl_setopt($ch, $opt_defined, $val);
                    }

                }
            }

            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $id;
        }

        $responses = array();
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
            if ($code != CURLM_OK) {
                break;
            }

            // 找出当前已经完成的请求
            while ($done = curl_multi_info_read($queue)) {
                $data = curl_multi_getcontent($done['handle']);
                $id = $map[(string) $done['handle']];

                // 是否使用回调函数
                if($callback != null) {
                    // 异步立即处理当前请求
                    $ret = array(
                        'ret' => array(
                            'id' => $id,
                            'delay' => $delay,
                            'response' => $data,
                        )
                    );
                    call_user_func_array($callback, $ret);
                }
                // 移除已经处理完毕请求
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);

                $responses[$id] = $data;
            }
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }

        } while ($active);
        curl_multi_close($queue);

        // 返回批量响应结果
        return $responses;
    }


    /**
     * 解析rest api返回
     * @param string $data
     * @param string $code
     * @param string $msg
     * @param string $data
     * @return mixed
     */
    public static function parse($result = '', $code = 'code', $msg = 'msg', $data = 'data')
    {
        $json = json_decode('{"error":0,"msg":"success","code":1000}');

        // 返回数据为空
        if(empty($result)) {
            $json->error += 1;
            $json->msg = 'The api return empty !';
            return $json;
        }

        // 返回数据格式错误
        $arr = json_decode($result, true);
        if(json_last_error() != 0) {
            $json->error += 1;
            $json->msg = 'Can\'t parse the result of api return !';
            return $json;
        }

        // 返回数据状态码错误
        if(!isset($arr[$code]) || $arr[$code] != 0) {
            $json->error += 1;
            $json->msg = !empty($arr[$msg]) ? $arr[$msg] : 'The api return an Invalid code !';
            return $json;
        }

        $json->code = $arr[$code];
        $json->data = !empty($arr[$data]) ? $arr[$data] : '';

        return $json;
    }

}