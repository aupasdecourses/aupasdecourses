<?php

/* DELETE 'V3' ! */

error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);

$directory = ("./attachmentsV3");

$folderRemb =("{$directory}/imgRemboursement");

if(!is_dir($folderRemb))
{
	$oldmask = umask(0);
	mkdir($folderRemb, 0777, true);
	umask($oldmask);
}

$content = opendir($directory);
$files = array();

while(($file = readdir($content)) !== FALSE)
{
	if($file != '.' && $file != '..')
	{
		if(!is_dir($directory.'/'.$file)) 
		{
			$files[] = $file;
		}
	}
}
closedir($content);

if (!empty($files))
{
	sort($files);
	/*
	** TRAITEMENT DES _TICKET.JPEG
	*/
	foreach($files as $value)
   	{
		// $link == "2016XX.jpeg" OU "2016XX_remb.png" OU "PNG/PDF/JPG..." 
		$link = preg_replace('/_ticket/','',$value);

		// $link == "2016XX.jpeg"
		if(substr($link, -5) == ".jpeg")
		{
			// $folder == "2016XX"
			$folder = preg_replace('/.jpeg/','',$link);

			// $folderRoute == "./attach/2016XX"
			$folderRoute = ("./attachmentsV3/{$folder}");

			$oldmask = umask(0);
			mkdir($folderRoute, 0777, true);
			umask($oldmask);
				
			// 2016XX/2016XX_ticket.jpeg --> 2016XX/2016XX.jpeg 
			if(rename("./attachmentsV3/{$value}" , "$folderRoute/$link"))
			{
				echo("Creation du repertoire et déplacement de l'image effectués pour : ".$link."<br/>");
			}
		}
	}
	/*
	** TRAITEMENT DES _REMB.PNG
	*/
	foreach($files as $value)
	{
		if(substr($value, -9) == "_remb.png" && rename("./attachmentsV3/{$value}", "$folderRemb/$value"))
				echo("Déplacement de l'image ".$value." effectué dans le répertoire ".$folderRemb."<br/>");
	}
	/*
	** TRAITEMENT DES XX.PNG (SIMILAIRE À _REMB)
	*/
	foreach($files as $value)
	{
		if(strlen($value) == 7 && substr($value, -3) == "png" && rename("./attachmentsV3/{$value}", "$folderRemb/$value"))
					echo("Déplacement de l'image ".$value." effectué dans le répertoire ".$folderRemb."<br/>");
	}	
	/*
	** TRAITEMENT DES XX.JPG (SIMILAIRE A _TICKET)
	*/ 	
	foreach($files as $value)
	{
		if(substr($value, -4) == ".JPG" && strlen($value) == 7)
		{
				$newValue = "2015000".$value;

				$folder = preg_replace('/.JPG/','', $newValue);
				$folderName = ("./attachmentsV3/{$folder}");
				
				$oldmask = umask(0);
				mkdir($folderName, 0777, true);
				umask($oldmask);
	 
				 if(rename("./attachmentsV3/{$value}" , "$folderName/2015000{$value}"))
				{
					echo("Creation du repertoire et déplacement de l'image effectués pour : ".$newValue."<br/>");
				}			
		}
	}
}
?>
