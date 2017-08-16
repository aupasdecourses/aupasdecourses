<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
/**
 * Add 'custom_attribute' attribute for entities
 */
$entities = array(
    'quote',
    //'order', // @TODO : a activer dans un nouveau fichier upgrade apres suppression de son equivalent amorderattr
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'visible'  => true,
    'required' => false,
	'default'  => 1
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'produit_equivalent', $options);
}
$installer->endSetup();