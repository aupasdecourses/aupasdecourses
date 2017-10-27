<?php

/*
 * Convertit les dates 'd/m/Y' de la table indi_billingdetails en 'Y-m-d', qui est le bon format pour la BDD
 */
$details = Mage::getModel('pmainguet_delivery/indi_billingdetails')->getCollection();

foreach ($details as $detail) {

	$old_creation_date = date_create_from_format('d/m/Y', $detail->getCreationDate());
	$new_creation_date = date_format($old_creation_date, 'Y-m-d');
	$detail->setCreationDate($new_creation_date);

	$old_delivery_date = date_create_from_format('d/m/Y', $detail->getDeliveryDate());
	$new_delivery_date = date_format($old_delivery_date, 'Y-m-d');
	$detail->setDeliveryDate($new_delivery_date);

	$old_billing_month = date_create_from_format('d/m/Y', $detail->getBillingMonth());
	$new_billing_month = date_format($old_billing_month, 'Y-m-d');
	$detail->setBillingMonth($new_billing_month);

	$detail->save();	
}
