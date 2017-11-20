<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'email_hipay',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 
        'after' => 'code',
        'comment' => 'Email Hipay',
    ]
);

$installer->getConnection()->addColumn(
    $tableName,
    'mdp_hipay',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255, 
        'after' => 'code',
        'comment' => 'Mdp Hipay',
    ]
);