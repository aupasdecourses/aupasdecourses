<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("Adyen.php");

try {

$run = new Adyen;

$value = $_POST['value'];
$iban = $_POST['iban'];
$ownerName = $_POST['ownerName'];
$merchantAccount = $_POST['merchantAccount'];
$reference = $_POST['reference'];
$shopperEmail = $_POST['shopperEmail'];
$shopperReference = $_POST['shopperReference'];

$run->payout($value, $iban, $ownerName, $merchantAccount, $reference, $shopperEmail, $shopperReference);
print_R($run);
} catch (Exception $e) {
	echo $e->getMessage();
}

?>
