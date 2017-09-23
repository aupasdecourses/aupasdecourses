<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$tableName = $installer->getTable('apdc_commercant/bankInfo');
$installer->getConnection()->addColumn(
    $tableName,
    'licence',
    ['type' => Varien_Db_Ddl_Table::TYPE_TEXT, 'length' => 255, 'after' => 'status', 'comment' => 'Licence']
);
