<?php
set_time_limit(0);
require_once('class.marketing.php');
$mobiles = '13818164082,15216647435,15000751382';
$content = '快宝公司通知yingxiao短信';

$obj = new marketing();
$obj->send($mobiles,$content);