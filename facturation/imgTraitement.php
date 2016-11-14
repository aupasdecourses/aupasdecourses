<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

$directory = ("./attachmentsV2");
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
	foreach($files as $value)
   	{
		/* $link == "2016XX.jpeg" OU "2016XX_remb.png" OU "PNG/PDF/JPG..." */
		$link = preg_replace('/_ticket/','',$value);

		// $link == "2016XX.jpeg"
		if(substr($link, -5) == ".jpeg")
		{
			// $folder == "2016XX"
			$folder = preg_replace('/.jpeg/','',$link);

			// $folderRoute == "./attach/2016XX"
			$folderRoute = ("./attachmentsV2/{$folder}");

			$oldmask = umask(0);
			mkdir($folderRoute, 0777, true);
			umask($oldmask);
				
			// 2016XX/2016XX_ticket.jpeg --> 2016XX/2016XX.jpeg 
			if(rename("./attachmentsV2/{$value}" , "$folderRoute/$link"))
			{
				echo("Creation du repertoire et déplacement de l'image effectués pour : ".$link."<br/>");
			}
		}
	}
}
?>
