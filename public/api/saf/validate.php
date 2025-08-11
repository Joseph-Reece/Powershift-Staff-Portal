<?php
include 'conn.php';
header("Content-Type:application/json");
$response = array();

date_default_timezone_set('Africa/Nairobi');
$dates = date('m/d/Y h:i a', time());

$ip = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');

date_default_timezone_set('Africa/Nairobi');
$tarehe = date('m/d/Y h:i:s a', time());
$logi = $ip.' - '.$tarehe.' - '.file_get_contents("php://input");

file_put_contents('validation_logs.txt', $logi.PHP_EOL, FILE_APPEND);


$jdata = json_decode(file_get_contents('php://input'), true);

	$TransactionType = $jdata ['TransactionType'];
	$TransID= $jdata ['TransID'];
	$TransTime = $jdata ['TransTime'];
	$TransAmount= $jdata ['TransAmount'];
	$BusinessShortCode = $jdata ['BusinessShortCode'];
	$InvoiceNumber= $jdata ['InvoiceNumber'];
	$OrgAccountBalance = $jdata ['OrgAccountBalance'];
	$ThirdPartyTransID= $jdata ['ThirdPartyTransID'];
	$MSISDN = $jdata ['MSISDN'];
	$FirstName = $jdata ['FirstName'];
	$MiddleName= $jdata ['MiddleName'];
	$LastName = $jdata ['LastName'];
	$BillRefNumber = strtoupper($jdata ['BillRefNumber']);
	
	$response["ResultCode"] = 0;
    $response["ResultDesc"] = 'Validation passed successfully';	
	echo json_encode($response);
?>