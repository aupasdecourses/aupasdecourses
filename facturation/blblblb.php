<?php

$result = '{
			"pspReference":"8614786933763431",
			"resultCode":"[payout-submit-received]"
		}';

var_dump($result);

$jsonDecoded = json_decode($result, true);



echo"<pre>";
print_R($jsonDecoded);
echo"</pre>";



$jsonDecoded["merchantAccount"] ="AuPasDeCoursesFR";
$jsonDecoded["originalReference"] = $jsonDecoded["pspReference"];
unset($jsonDecoded["pspReference"]);

unset($jsonDecoded["resultCode"]);



echo"<pre>";
print_R($jsonDecoded);
echo"</pre>";

$finally = json_encode($jsonDecoded);

var_dump($finally);
