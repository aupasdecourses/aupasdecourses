<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'incremental',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'after' => 'id_attribut_commercant',
        'comment' => 'SKU Incremental',
        'default' => 1000,
    ]
);