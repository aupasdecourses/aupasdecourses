<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("Adyen.php");

try {

$run = new Adyen;

$value = $_POST['value'];
$merchantAccount = $_POST['merchantAccount'];
$originalReference = $_POST['originalReference'];
$reference = $_POST['reference'];

$run->refund($merchantAccount, $value, $originalReference, $reference);
print_R($run);
} catch (Exception $e) {
	echo $e->getMessage();
}

?>
