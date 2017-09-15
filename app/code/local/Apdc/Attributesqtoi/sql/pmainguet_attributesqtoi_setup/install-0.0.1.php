<?php

/*Installed through backoffice

// $installer = $this;

// $installer->addAttribute('catalog_product', 'commercant', array(
//     'group'             => 'Infos Produits',
//     'type'              => Varien_Db_Ddl_Table::TYPE_VARCHAR,
//     'label'             => 'Commerçant',
//     'input'             => 'text',
//     'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
//     'visible'           => true,
//     'required'          => true,
//     'unique'            => false,
//     'apply_to'          => 'simple,configurable,virtual',
//     'is_configurable'   => false
// ));
*/

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');
/**
 * Add 'custom_attribute' attribute for entities
 */
$entities = array(
    'quote_item',
    'order_item',
);
$options = array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'visible'  => true,
    'required' => false
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'commercant', $options);
    $installer->addAttribute($entity, 'marge_arriere', $options);
}
$installer->endSetup();
