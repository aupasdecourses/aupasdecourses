<?php

$directory = ("./attachments");

$content = opendir($directory);
$files = array();
$folders = array();

while(($file = readdir($content)) !== FALSE) {
			if($file != '.' && $file != '..'){
				
				if(!is_dir($directory.'/'.$file)) {
					$files[] = $file;
				} else { $folders = $file;
				}
			}
			}
		closedir($content);

if (!empty($files)){
	sort($files);

	foreach($files as $link) {
		echo "{$directory}/{$link}<br/>".PHP_EOL;
	}
}
?>
