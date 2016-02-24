<?php
function header_status($status)
{
   if (substr(php_sapi_name(), 0, 3) == 'cgi')
   header('Status: '.$status, TRUE);
   else
   header($_SERVER['SERVER_PROTOCOL'].' '.$status);
}
header_status('404 Not Found');
require 'statics/images/404.html';
die;