<?php


$installer = $this;

$installer->addAttribute('catalog_product', 'commercant_id', array(
    'group'             => 'Infos Produits',
    'type'              => Varien_Db_Ddl_Table::TYPE_INT,
    'label'             => 'Commerçant',
    'input'             => 'int',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => true,
    'unique'            => false,
    'apply_to'          => 'simple,configurable,virtual',
    'is_configurable'   => false
));

$installer->endSetup();