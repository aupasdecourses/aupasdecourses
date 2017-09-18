<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');


$installer->getConnection()->addColumn(
    $tableName,
    'flag_magmi',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment' => 'Flag Magmi'
    ]
);