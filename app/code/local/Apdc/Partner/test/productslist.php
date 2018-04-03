<?php 
$key = 'd90e49032622e93040bca7c3e6815b7f';
$secret = 'eaa49a6c5ae296fadf5b7f833e384854';
$email = 'contact@aupasdecourses.com';
$tz = 'Europe/Paris';
$dt = new DateTime('now', new DateTimeZone($tz)); //first argument "must" be a string
$data = $key . $secret . $dt->format('Y-m-d');
$signature = base64_encode(hash_hmac('sha256', $data, $email, true));

$postData = [
    'key' => $key
];
$header = [
    'Authorization: Bearer ' . $signature
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://dev.aupasdecourse.com/accueil/partner/product/list');
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "\n";
print_r($response);
echo "\n";

curl_close($ch);
