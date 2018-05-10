<?php

// 1 - Delete old column from "indi_billing_summary_table"
// 2 - Create new label "merchant_bill_not_visible"

// 1 - A - Init
$installer = $this;
$installer->startSetup();

// 1 - B - Remove old column
$installer->getConnection()->dropColumn($installer->getTable('pmainguet_delivery/indi_billingsummary'), 'merchant_bill_comment');
$installer->endSetup();


// 2 - A - Init
$type_model = Mage::getModel('pmainguet_delivery/indi_commenttype');
$types = [
    0 => ['type' => 'merchant_not_visible', 		'label' => 'Commercant interne'],
    1 => ['type' => 'customer_not_visible', 		'label' => 'Client interne'],
    2 => ['type' => 'mixed_not_visible',    		'label' => 'Mixte interne'],
    3 => ['type' => 'merchant_bill_not_visible', 	'label' => 'Facturation commercant interne'],
    4 => ['type' => 'customer_is_visible',  		'label' => 'Client visible'],
];

// 2 - B - Truncate table
foreach ($type_model->getCollection() as $type) {
	$type->delete();
}

// 2 - C - Fill with new proper types
if ($type_model->getCollection()->getSize() === 0) {
	try {
		foreach ($types as $t) {
			$type_model->setData([
				'type'		=> $t['type'],
				'label'		=> $t['label'],
			])->save();
		}
	} catch (\Exception $e) {
		echo $e->getMessage();
	}
}