<?php

//Global var

	//To get rid of loading time on Google Sheets side (if file less than 1Ko, retry)
	$size_file_limit=1000;

	$url_base="/home/aupasdecln/www/var/import/";

	$google_csv=[
		"FRB17"=>[
			"key"=>"1TzE4XZu0QeqKYDergYwfMwnvVHKLPXSMvc2fTxRueH0",
			"gid"=>"186615455"
		],
		"RGA17"=>[
			"key"=>"1VCEWDmHeT2AROPdhybS-t1ggYA6UUi2EzHBeUtJOIbs",
			"gid"=>"1539964024"
		],
		"CDV17"=>[
			"key"=>"1pYtUc3U59Vq8LAcPgmxjtQw2qOHBnKUyiKTp0z-5twI",
			"gid"=>"1504362974"
		],
		"FIB17"=>[
			"key"=>"1YETjLO70-eBtJWpE1k-4Nw1LqjAy_jz29w7Nof32wf4",
			"gid"=>"1218336066"
		],
		"PBP17"=>[
			"key"=>"1uCsvSg8x9a7DhF-9VTMw35lJ4oEryK-e2Kw6yc7x2jo",
			"gid"=>"2030347927"
		],
		"MAR17"=>[
			"key"=>"1B1eWstCOVbr-ZhAcPwZVwzpdFvKzCBMXOMvd5dmdf4Q",
			"gid"=>"2131873323"
		],
		"BDM17"=>[
			"key"=>"1bP2WLdxn0JtA13UG3CKC4DBbnkrTHiSpEH0J5BHmfcU",
			"gid"=>"1349743815"
		],
		"DAN17"=>[
			"key"=>"1dIal6coAGYc9x2mZ2Qasigg8iedK2Vt0GCko1WPc8G8",
			"gid"=>"1349743815"
		],
		"TDC17"=>[
			"key"=>"1wALZpT5p8FSE-f99vLquPDMHxG6sVumlPynlp5MGwyo",
			"gid"=>"2030347927"
		],
		"LRE17"=>[
			"key"=>"1E8RiNjExayaTUmZwAO1j2cfeiHwl7dnRBf2TWhWYo8w",
			"gid"=>"2030347927"
		],
		
	];

	if (isset($_GET['action'])) {
	$commercant=explode("|",$_GET['action']);
		foreach($commercant as $code){
			$key=$google_csv[$code]['key'];
			$gid=$google_csv[$code]['gid'];
		   	try{
		   		$filepath=$url_base.date('ymd_Hi')."_".$code.".csv";
		   		$filesize=0;
		   		while($filesize<$size_file_limit){
		   			file_put_contents($filepath, file_get_contents("https://docs.google.com/spreadsheets/d/".$key."/export?gid=".$gid."&format=csv&id=".$key));
		   			$filesize=filesize($filepath);
		   		}
		   		echo "Fichiers ".$code." synchronisés! (taille=".round(floatval($filesize)/1000,0)."Ko)";
		   	}catch(Exception $e){
		   		echo "Erreur!";
		   	}
	   }
	}
?>