<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("Adyen.php");

try {

	$run = new Adyen;

	$merchantAccount = $_POST['merchantAccount'];
	$contract = $_POST['contract'];
	$shopperReference = $_POST['shopperReference'];

	$run->listRecurringDetails($merchantAccount, $contract, $shopperReference);
	print_R($run);
} catch (Exception $e){
	echo $e->getMessage();
}
?>
