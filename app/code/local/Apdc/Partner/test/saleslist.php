<?php 
$key = 'e6607e00e283344614586a755e97d065';
$secret = 'f67740ba2c25c7ed695da47817df3879';
$postData = array(
    'key' => $key,
    //'from' => '2018-03-12 00:00:00', // optionnal format : Y-m-d H:i:s
    //'to' => '2018-03-15 23:59:59', //optionnal format : Y-m-d H:i:s
    //'quote_ids' => '8285,8282,8268' // optionnal : list of quote ids comma separated
    //'quote_ids' => '8285',
    'from' => date('Y-m-d') . ' 00:00:00'
);
$email = 'mysthr21@gmail.com';
$data = $key . $secret . date('Y-m-d');
$signature = base64_encode(hash_hmac('sha256', $data, $email, true));

$header = [
    'Authorization: Bearer ' . $signature
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://dev.aupasdecourses.local/accueil/partner/sales/list");
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "\n";
print_r($response);
echo "\n";

print_r(json_decode($response, true));

curl_close($ch);
