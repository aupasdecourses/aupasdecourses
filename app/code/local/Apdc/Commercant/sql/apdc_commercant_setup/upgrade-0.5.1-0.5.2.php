<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'enabled',
    ['type' => Varien_Db_Ddl_Table::TYPE_INTEGER, 'after' => 'id_commercant', 'default' => 0, 'comment' => 'Enabled']
);

$installer->getConnection()->addColumn(
    $tableName,
    'id_contact_employee_bis',
    ['type' => Varien_Db_Ddl_Table::TYPE_INTEGER, 'after' => 'id_contact_employee', 'comment' => 'Contact employee bis']
);
$installer->getConnection()->addForeignKey(
    $installer->getFkName('apdc_commercant/shop', 'id_contact_employee_bis', 'apdc_commercant/contact', 'id_contact'),
    $tableName, 'id_contact_employee_bis', $installer->getTable('apdc_commercant/contact'), 'id_contact'
);

$installer->getConnection()->addColumn(
    $tableName,
    'delivery_days',
    ['type' => Varien_Db_Ddl_Table::TYPE_TEXT, 'length' => 255, 'after' => 'timetable', 'comment' => 'Delivery days']
);
