<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'cpte_hipay',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 25, 
        'after' => 'code',
        'comment' => 'Compte Hipay',
    ]
);
$installer->getConnection()->addColumn(
    $tableName,
    'cpte_compta',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 25, 
        'after' => 'code',
        'comment' => 'Compte Comptabilité APDC',
    ]
);