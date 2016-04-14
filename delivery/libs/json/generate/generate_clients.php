<?php


ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
/*
* CSV2JSON PHP script
* This script converts a CSV file to a JSON object. It expects all values to
* be enclosed in double quotes (") and to be seperated by commas (,)
*
* Written by Hay Kranen < http://www.haykranen.nl > < hay at bykr dot org >
*
* USE
* Make this executable
* $ chmod u+x csv2json
* Then invoke it from the command line like this
* $ csv2json myfile.csv
* The script will automatically write the resulting file as myfile.json
*/
function quit($msg) {
	die($msg . "\n");
}

// For PHP < 5.3
if (!function_exists('str_getcsv')) {
	function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
		$fiveMBs = 5 * 1024 * 1024;
		$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
		fputs($fp, $input);
		rewind($fp);
		$data = fgetcsv($fp, 1000, $delimiter, $enclosure); // $escape only got added in 5.3.0
		fclose($fp);
		return $data;
	}
}

function geocode_address($adresse) {
	$data=[];
	$adresse=urlencode(htmlentities($adresse));
	$query='http://nominatim.openstreetmap.org/search?format=json&q='.$adresse.'&countrycodes=fr';
	$string = file_get_contents($query);
	$json = json_decode($string, true);
	return $json;
}


//$filename = $argv[1];
$filename='../source/clients.csv';

if (!file_exists($filename)) quit("File does not exist: $filename");

$file = file($filename);

if (!$file) quit("File read error with $filanme");

echo "Processing $filename... <br/><br/>";

$data = array();

// Get the first line for the values
$hash = str_getcsv($file[0]);
unset($file[0]);

foreach ($file as $line) {
	// Create new array for the line
	$values = str_getcsv($line);
	$obj = array();
	foreach ($values as $index => $value) {
		$key = $hash[$index];
		$obj[$key] = $value;
	}
	if($obj["Adresse"]!=""){
		$json=geocode_address(htmlentities($obj['Adresse']));
		$obj['lat']=floatval($json[0]['lat']);
		$obj['lon']=floatval($json[0]['lon']);
		$data[] = $obj;
		echo $obj["Nom"].' géocodé à l\'adresse '.$obj["Adresse"].'!</br>';
	}else{
		echo "Aucune adresse pour ".$obj['Nom'].". Entrée ignorée.</br>";
	}
}

echo "Ready..now writing to JSON<br/><br/>";

$json = json_encode($data);

if (!$json) quit("Could not encode to JSON");

// Create a filename based on the input file
$out = "../".pathinfo($filename, PATHINFO_FILENAME) . ".json";

//if(file_exists($out)) quit("File already exists! Aborting!");

if(!file_put_contents($out, $json)) quit("Could not write contents to $out");

echo "Written JSON data to $out</br></br>";