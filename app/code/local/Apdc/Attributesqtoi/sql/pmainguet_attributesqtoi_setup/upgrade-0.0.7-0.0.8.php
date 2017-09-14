<?php

/* Add 'produit_fragile' attribute */


$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'produit_fragile');
$installer->addAttribute('catalog_product', 'produit_fragile', array(
	'group'						=> 'Infos Produits',
	'type'						=> 'int',
	'input'						=> 'select',
	'label'						=> 'Produit fragile',
	'global'					=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'					=> 1,
	'required'					=> 0,
	'visible_on_front'			=> 1,
	'is_configurable'			=> 0,
	'source'					=> 'eav/entity_attribute_source_boolean',
	'unique'					=> false,
	'user_defined'				=> true,
	'is_user_defined'			=> true,
	'used_in_product_listing'	=> true,
	'default'					=> 0,
	'apply_to'					=> 'simple,configurable,virtual',

));
$installer->endSetup();

$installer2 = new Mage_Sales_Model_Resource_Setup('core_setup');

$entities = array(
	'quote_item',
	'order_item',
);
$options = array(
	'type'		=> Varien_Db_Ddl_Table::TYPE_TEXT,
	'visible'	=> true,
	'required'	=> false
);
foreach ($entities as $entity) {
	$installer2->addAttribute($entity, 'produit_fragile', $options);
}
