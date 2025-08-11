<?php
$serverName = "erpserver"; //serverName\instanceName
$connectionInfo = array( "Database"=>"MCK ERP", "UID"=>"sa", "PWD"=>"System254");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
     $sql= "SELECT * FROM [MCK ERP].[dbo].[MCK".'$'."SMS Sender] WHERE [Status] = 1 ";
	 $qry = sqlsrv_query($conn, $sql);
	 
	 while($row=sqlsrv_fetch_array($qry)){
		 sendsms($row['PhoneNo'], $row['Message'], $row['Entry No']);
	 }
}

function sendsms($phone, $message, $entryno){
	global $conn;
	$phone1 = "+$phone";
	$message1 = urlencode($message);

	$account_id = 'zWMwWOWu1ckUE0ddCvCA';
	$account_secret = 'GTtLn2yVeLOipxvhYBkT4xNGsV8trKnHu8dy292k';

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"https://api.smsleopard.com/v1/sms/send?message=$message1&destination=$phone1&source=MCK");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '.base64_encode($account_id.':'.$account_secret), 'Content-Type:application/json']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	// grab URL and pass it to the browser
	$output = curl_exec($ch);
	// close cURL resource, and free up system resources
	curl_close($ch);
	
	$jdata = json_decode($output, true);
	$retmessage = $jdata['message'];
	//update table
	$sql1 = "UPDATE [MCK ERP].[dbo].[MCK".'$'."SMS Sender] SET [Status] = 2, [Date Sent] = CURRENT_TIMESTAMP, [Return Message] = '$retmessage' where [Entry No] = '$entryno'";
	$qry1 = sqlsrv_query($conn, $sql1);
	
	sqlsrv_close($conn);
}
?>