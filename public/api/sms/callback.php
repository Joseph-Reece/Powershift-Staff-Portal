<?php

date_default_timezone_set('Africa/Nairobi');
$dates = date('m/d/Y h:i a', time());

$response = array();
$ip = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');

$tarehe = date('m/d/Y h:i:s a', time());
$logi = $ip.' - '.$tarehe.' - '.file_get_contents("php://input");

file_put_contents('stkcallbacklogs.txt', $logi.PHP_EOL, FILE_APPEND);

?>