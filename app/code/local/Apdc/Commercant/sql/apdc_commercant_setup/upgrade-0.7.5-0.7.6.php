<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'external_id',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'after' => 'code',
        'length' => 30,
        'default' => '',
        'comment' => 'Id magasin partenaire'
    ]
);

$installer->endSetup();
