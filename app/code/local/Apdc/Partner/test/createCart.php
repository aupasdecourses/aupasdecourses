<?php 
$key = '9f67bed68611abc5608a743626ff52ca';
$secret = 'cb84186afe6e3fb37801511bf21fbc3a';
$email = 'mysthr21@gmail.com';
$data = $key . $secret . date('Y-m-d');
$signature = base64_encode(hash_hmac('sha256', $data, $email, true));

$userData = array(
    'key' => $key,
    'customer_id' => null,
    'postcode' => '75017',
    'products[4107]' => 1,
    'products[4111]' => 1,
    'products[4851]' => 2,
    'products[7146]' => 1
);

$header = [
    'Content-type: multipart/form-data',
    'Authorization: Bearer ' . $signature
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://dev.aupasdecourses.local/paris17e/partner/cart/create");
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $userData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
echo "\n";
print_r($response);
echo "\n";
/*
    $response : json
    [
        'quote_id' => (int),
        'redirect_url' => (string)
    ]
*/
$response = json_decode($response, true);
print_r($response);
//if ($response['redirect_url']) {
    //header('Location: ' . $response['redirect_url']);
//}

curl_close($ch);
