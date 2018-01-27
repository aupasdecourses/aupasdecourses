<?php

$data=array(
	array(1,'Boucher'),
	array(2,'Primeur'),
	array(3,'Fromager'),
	array(4,'Boulanger'),
	array(5,'Caviste'),
	array(6,'Epicier'),
	array(7,'Poissonnier')
);

foreach($data as $row){
	$shops = Mage::getModel('apdc_commercant/typeshop');
	$shops->setData('id_type',$row[0]);
	$shops->setData('label',$row[1]);
	$shops->save();
}