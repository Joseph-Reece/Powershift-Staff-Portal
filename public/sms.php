<?php
    file_put_contents('sms-errors.log', "connected\n", FILE_APPEND);
    $to = $_POST['to'];
    $message = $_POST['message'];
    $apiKey = $_POST['apiKey'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $data = [
        'to' => $to,
        'message' => $message,
        'username' => $username,
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.africastalking.com/version1/messaging');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $headers[] = 'Accept: application/json';
    $headers[] = 'apiKey: '.$apiKey;
    // $headers[] = 'authorization:Bearer '.$apiKey;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if($password == 'kaywide'){
        $result = curl_exec($ch);
        file_put_contents('sms-errors.log', "$result\n", FILE_APPEND);
        //return true;
    }
    if (curl_errno($ch)) {
        file_put_contents('sms-errors.log', curl_error($ch) . "\n", FILE_APPEND);
        //return false;
    }
    curl_close($ch);
?>
