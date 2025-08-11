<?php

include 'conn.php';

header("Content-Type:application/json");
date_default_timezone_set('Africa/Nairobi');
$dates = date('m/d/Y h:i a', time());

$response = array();
$ip = getenv('HTTP_CLIENT_IP')?:
getenv('HTTP_X_FORWARDED_FOR')?:
getenv('HTTP_X_FORWARDED')?:
getenv('HTTP_FORWARDED_FOR')?:
getenv('HTTP_FORWARDED')?:
getenv('REMOTE_ADDR');

date_default_timezone_set('Africa/Nairobi');
$tarehe = date('m/d/Y h:i:s a', time());
$logi = $ip.' - '.$tarehe.' - '.file_get_contents("php://input");

file_put_contents('confirmation_logs.txt', $logi.PHP_EOL, FILE_APPEND);

$jdata = json_decode(file_get_contents('php://input'), true);

	$TransactionType = $jdata ['TransactionType'];
	$TransID= $jdata ['TransID'];
	$TransTime = $jdata ['TransTime'];
	$TransAmount= $jdata ['TransAmount'];
	$BusinessShortCode = $jdata ['BusinessShortCode'];
	$BillRefNumber= $jdata ['BillRefNumber'];
	$InvoiceNumber= $jdata ['InvoiceNumber'];
	$OrgAccountBalance = $jdata ['OrgAccountBalance'];
	$ThirdPartyTransID= $jdata ['ThirdPartyTransID'];
	$MSISDN = $jdata ['MSISDN'];
	$FirstNames = $jdata ['FirstName'];
	$MiddleNames= $jdata ['MiddleName'];
	$LastNames = $jdata ['LastName'];
	
	$SQL = "INSERT INTO payments
	(trans_type,trans_id,trans_time,trans_amount,business_short_code,bill_ref_no,invoice_no,org_account_balance,third_party_trans_id,msisdn,first_name,middle_name,last_name) 
	VALUES
	('M-Pesa',$TransID, CURRENT_TIMESTAMP, $TransAmount,$BusinessShortCode,$BillRefNumber,0,$OrgAccountBalance,0,$MSISDN,$FirstNames,$MiddleNames,$LastNames)"; 
	$qry = $conn->query($SQL);
	file_put_contents('loggss.txt', $SQL.PHP_EOL, FILE_APPEND);
	if($qry){
		//UpdateNAV();
		$response["ResultCode"] = 0;
		$response["ResultDesc"] = 'Confirmation Received successfully';	
		echo json_encode($response);
	}else{
		$response["ResultCode"] = 1;
		$response["ResultDesc"] = 'Confirmation Failed';	
		echo json_encode($response);
		
	}
?>