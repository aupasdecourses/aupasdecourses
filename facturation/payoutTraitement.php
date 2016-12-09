<?php

// HTML ARRAY FROM POST FORM
$htmlArray = array (
	"amount" => array(
		"currency" => $_POST['currency'],
		"value" => $_POST['value']
		),
	
	"bank" => array(
		"iban" => $_POST['iban'],
		"ownerName" => $_POST['ownerName'],
		"countryCode" => $_POST['countryCode']
		),
	
	"merchantAccount" => $_POST['merchantAccount'],

	"recurring" => array(
		"contract" => $_POST['contract']
		),

	"reference" => $_POST['reference'],
	"shopperEmail" => $_POST['shopperEmail'],
	"shopperReference" => $_POST['shopperReference']
	);

echo'<pre>';
print_r ($htmlArray);
echo'</pre>';

// WRITE JSON TABLE IN STORE&SUBMIT.JSON
$file = 'storeAndSubmit.json';
$contents = file_get_contents($file , 'w+');
$contentsDecoded = json_decode($contents, true);
$contentsDecoded = $htmlArray;
$json = json_encode($contentsDecoded);
$fp = file_put_contents($file , $json);


// CURL METHOD TO SEND JSON TO ADYEN
$ch = curl_init();

if(FALSE === $ch)
	throw new Exception('failed to initialize');


curl_setopt($ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/storeDetailAndSubmitThirdParty");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));

curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

curl_setopt($ch, CURLOPT_USERPWD, "storePayout_104791@Company.AuPasDeCourses:9GsnR!sm3]*w7>rh%^bHSd!@2");

curl_setopt($ch, CURLOPT_POST,true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $json);


$result = curl_exec($ch);

if(curl_errno($ch)) {
	print "Error: ". curl_error($ch);
} else {
	var_dump($result);
	curl_close($ch);
}


?>
