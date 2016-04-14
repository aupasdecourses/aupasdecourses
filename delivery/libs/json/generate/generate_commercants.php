<?php

function connect_magento(){
  include '../../../../commis2/app/Mage.php';
  umask(0);
  Mage::app();
}

function liste_cat_commercant(){
    $storeCode = Mage::app()->getStore()->getCode();
    $helper= Mage::helper('catalog/category');
    $cat_list= $helper->getStoreCategories();
    foreach($cat_list as $cat){
        $id=$cat->getId();
        $cat = Mage::getModel('catalog/category')->load($id);
        $return[$id]=$cat->getName();
    }

    return $return;
}

//LISTE COMMERCANT
function liste_commercants($id){
    $cat = Mage::getModel('catalog/category')->load($id);
    $children_cat=$cat->getChildrenCategories();
    return $children_cat;
}

//RECUPERER COMMERCANT AVEC ID
function commercant($id){
    $cat = Mage::getModel('catalog/category')->load($id);
    return $cat;
}

connect_magento();
$cat=liste_cat_commercant();
$commercant=array();

echo "Processing commercants list ... <br/><br/>";

$json='[';

foreach($cat as $id => $name){
	$cat=liste_commercants($id);
	foreach($cat as $t){
		$com_adresse=urlencode(commercant($t->getId())->getData('adresse_commercant'));
		//get lat-lon coordinates from Nominatim service
		$string=file_get_contents('http://nominatim.openstreetmap.org/?format=json&addressdetails=1&q='.$com_adresse);
		$response = json_decode($string, true);
		$json.='{"id":"'.$t->getId().'","nom":"'.$t->getName().'","adresse":"'.$com_adresse.'","categorie":"'.$name.'","lat":"'.$response[0][lat].'","lon":"'.$response[0][lon].'"},';
	}
}

$json=rtrim($json,',');

$json.=']';

echo "Ready..now writing to JSON<br/><br/>";

$fp = fopen('../commercants.json', 'w');
fwrite($fp, $json);
fclose($fp);
?>