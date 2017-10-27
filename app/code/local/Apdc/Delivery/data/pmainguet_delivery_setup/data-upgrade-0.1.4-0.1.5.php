<?php

/*
 * Convertit les dates 'd/m/Y' de la table indi_billingdetails en 'Y-m-d', qui est le bon format pour la BDD
 */
$details = Mage::getModel('pmainguet_delivery/indi_billingdetails')->getCollection();

foreach ($details as $detail) {

	if (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/", $detail->getCreationDate())) {
		$old_creation_date = date_create_from_format('d/m/Y', $detail->getCreationDate());
		$new_creation_date = date_format($old_creation_date, 'Y-m-d');
		$detail->setCreationDate($new_creation_date);
	}

	if (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/", $detail->getDeliveryDate())) {
		$old_delivery_date = date_create_from_format('d/m/Y', $detail->getDeliveryDate());
		$new_delivery_date = date_format($old_delivery_date, 'Y-m-d');
		$detail->setDeliveryDate($new_delivery_date);
	}

	$detail->save();	
}
