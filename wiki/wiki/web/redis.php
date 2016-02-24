<?php
/**
 * redis测试
 */
if(isset($_GET['i'])) {
    phpinfo();
    exit;
}

$redis = new Redis();

@$redis->connect('192.168.1.773', 6379, 1.2);
@$redis->set('test_key', 'test_val');
try{

} catch (Exception $e) {

}


echo '<pre>测试常规使用取值';
//var_dump($redis->get('mm'));

//$redis->del('test_key');
//echo '<pre>测试删除后取值';
//var_dump($redis->get('test_key'));
//
//
//$redis->lPush('test_queue_key', 'test_queue_val1');
//$redis->lPush('test_queue_key', 'test_queue_val2');
//
//echo '<pre>测试队列第1次取值';
//var_dump($redis->rPop('test_queue_key'));
//
//echo '<pre>测试队列第2次取值';
//var_dump($redis->rPop('test_queue_key'));
//
//echo '<pre>测试队列第3次取值';
//var_dump($redis->rPop('test_queue_key'));