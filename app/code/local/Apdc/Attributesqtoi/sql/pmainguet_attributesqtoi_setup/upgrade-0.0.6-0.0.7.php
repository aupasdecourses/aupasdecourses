<?php
//Remove totaly commercant_id (category) attribute 
$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');
$entities = array(
    'sales_flat_quote',
    'sales_flat_quote_address',
    'sales_flat_quote_item',
    'sales_flat_quote_address_item',
    'sales_flat_order',
    'sales_flat_order_item',
);
foreach ($entities as $entity) {
    $installer->getConnection()->dropColumn($installer->getTable($entity), 'commercant_id');
}

$installer2 = new Mage_Eav_Model_Entity_Setup('core_setup');
$entities2 = array(
    'catalog_product',
);
foreach ($entities2 as $entity2) {
    $installer2->removeAttribute($entity2, 'commercant_id');
}

//Remove uneeded item attribute in non-item related table
$entities = array(
    'sales_flat_quote',
    'sales_flat_quote_address',
    'sales_flat_quote_address_item',
    'sales_flat_order',
);
$options = array(
    'commercant',
    'marge_arriere',
    'prix_kilo_site',
    'short_description',
);
foreach ($entities as $entity) {
	foreach ($options as $option) {
    	$installer->getConnection()->dropColumn($installer->getTable($entity), $option);
    }
}