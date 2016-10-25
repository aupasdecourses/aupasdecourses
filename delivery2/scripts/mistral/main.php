<?php

require "../../vendor/autoload.php";
require "../../module/Application/src/Controller/MagentoController.php";

require "../../../app/Mage.php";

Mage::app();

require "db.php";
require "ftp.php";

require "oe.php";
require "ol.php";

try {
//	$c_date = date("Y-m-d");
	$c_date = "2016-06-17";
	$c_time = date("His");
	$fileName = str_replace("-", "", $c_date) . "_APDC_CDE_{$c_time}.csv";
	$tmpFileName = "tmp.csv";

//	$db = new db('localhost', 'apdcdev', 'apdcdev', '9dRY6FEtwYP5WDGT');
//	$q = $db->queryAll("SELECT * FROM mistral");
//	var_dump($q);

	$magento_controller = new \Application\Controller\MagentoController();
	$q = $magento_controller->getMerchantsOrdersAction(true, -1, $c_date);

	$out = "";
	foreach($q as $k => $m) {
		foreach($m['orders'] as $i => $o) {
			$out .= oe($m, $o)."0".PHP_EOL;
			$out .= ol($m, $o)."0".PHP_EOL;
		}
	}
	if ($out <> "") {
		file_put_contents($tmpFileName, $out);
		system("cat {$tmpFileName}");
		echo "{$fileName}".PHP_EOL;
//		=== FTP ===
//		$mistral_ftp = new ftp("ftporamtl.stars-services.com");
//		$mistral_ftp->pasv(false);
//		$mistral_ftp->login("ftpapdc", "ftp.1a");
//		$mistral_ftp->put("IN/{$fileName}", $tmpFileName);
	}

} catch  (Exception $e) {
	echo "Error: {$e->getMessage()}" . PHP_EOL;
}
