<?php 
$key = 'e6607e00e283344614586a755e97d065';
$secret = 'f67740ba2c25c7ed695da47817df3879';
$userData = array(
    'key' => $key
);
$email = 'mysthr21@gmail.com';
$data = $key . $secret . date('Y-m-d');
$signature = base64_encode(hash_hmac('sha256', $data, $email, true));

$header = [
    'Authorization: Bearer ' . $signature
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://dev.aupasdecourses.local/paris17e/partner/product/list");
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $userData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "\n";
print_r($response);
echo "\n";

curl_close($ch);
