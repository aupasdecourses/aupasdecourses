<?php

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$entities = array(
	'quote',
	'order',
);

$options = array(
	'type'		=> Varien_Db_Ddl_Table::TYPE_INTEGER,
	'visible'	=> true,
	'required'	=> false,
	'nullable'	=> true,
	'default'	=> null,
);

foreach ($entities as $entity) {
	$installer->addAttribute($entity, 'commande_honoree', $options);
}

$installer->endSetup();
