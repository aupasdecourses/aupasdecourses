<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'type_shop',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'after' => 'enabled',
        'comment' => 'Type de Commerce',
    ]
);