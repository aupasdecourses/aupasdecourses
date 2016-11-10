<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);


$directory = ("./attachments");

$content = opendir($directory);
$files = array();

while(($file = readdir($content)) !== FALSE) {
			if($file != '.' && $file != '..'){
				
				if(!is_dir($directory.'/'.$file)) {
					$files[] = $file;
				}
			}
			}
		closedir($content);

if (!empty($files)){
	sort($files);

	foreach($files as $value) {

		// image sans "_ticket"		
		$link = preg_replace('/_ticket/','',$value);

		// dossiers correspondants aux noms d'img
		$folder = preg_replace('/.jpeg/','',$link);
		

		$folderName = ("./attachments/{$folder}");
	//	mkdir($folderName, 0777, true);

		rename("$value" , "$folderName/$folder.jpeg");

//		echo "{$directory}/{$link}<br/>".PHP_EOL;
//		echo "{$directory}/{$folder}<br/>".PHP_EOL;
	}
}
?>
