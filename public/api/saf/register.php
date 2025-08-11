<?php
//$url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
$url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer Uu2S7GCx5WzfBbzPJ9dQ4UGxgrV8'));


$curl_post_data = array(
  'ShortCode' => '5903755',
  'ResponseType' => 'Cancelled',
  'ValidationURL' => 'https://cryptohela.com/api/validate.php',
  'ConfirmationURL' => 'https://cryptohela.com/api/confirm.php'
);

$data_string = json_encode($curl_post_data);

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$curl_response = curl_exec($curl);
print_r($curl_response);

echo $curl_response;
?>
