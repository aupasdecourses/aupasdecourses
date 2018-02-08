<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'minimum_order',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 20,
        'after' => 'enabled',
        'comment' => 'Minimum order',
    ]
);
