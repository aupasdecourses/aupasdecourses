<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/* Contact table */
$tableName = $installer->getTable('apdc_commercant/typeshop');

if ($installer->tableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('id_type_shop', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false])
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255);
$installer->getConnection()->createTable($table);