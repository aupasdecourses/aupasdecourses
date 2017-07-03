<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->dropForeignKey(
        $tableName,
        $installer->getFkName('apdc_commercant/shop', 'id_category', 'catalog/category', 'entity_id')
    );

$installer->run("ALTER TABLE $tableName MODIFY COLUMN id_category VARCHAR(255);");

$installer->getConnection()->addColumn(
    $tableName,
    'stores',
    ['type' => Varien_Db_Ddl_Table::TYPE_TEXT, 'length' => 255, 'after' => 'id_category', 'comment' => 'Store Ids']
);