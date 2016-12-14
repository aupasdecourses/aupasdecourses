<?php

// HTML ARRAY FROM POST METHOD
$htmlArray = array(
	"merchantAccount" => $_POST['merchantAccount'],
	"originalReference" => $_POST['originalReference']
	);

echo'<pre>';
print_r($htmlArray);
echo'</pre>';

// WRITE JSON TABLE IN CONFIRM.JSON
$file ='confirm.json';
$contents = file_get_contents($file, 'w+');
$contentsDecoded = json_decode($contents, true);
$contentsDecoded = $htmlArray;
$json = json_encode($contentsDecoded);
$fp = file_put_contents($file, $json);

// CURL METHOD TO SEND CONFIRM TO ADYEN
$ch = curl_init();

if(FALSE === $ch)
	throw new Exception('failed to initialize');


curl_setopt($ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/confirmThirdParty");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));

curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

curl_setopt($ch, CURLOPT_USERPWD, "reviewPayout_092607@Company.AuPasDeCourses:BZYi/(p3h<BTA<v13At3@Dcj*");

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $json);


$result = curl_exec($ch);

if(curl_errno($ch)) {
	print "Error: ". curl_error($ch);
} else {
	var_dump($result);
	curl_close($ch);
}

?>
