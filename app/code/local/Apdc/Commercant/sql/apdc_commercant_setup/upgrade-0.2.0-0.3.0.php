<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/* Contact role table */
$tableName = $installer->getTable('apdc_commercant/contact_role');
$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false])
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
;

$installer->getConnection()->createTable($table);

$installer->getConnection()->insertArray(
    $tableName,
    ['name', 'label'],
    [
        ['ceo', 'Gérant'],
        ['billing', 'Contact facturation'],
        ['manager', 'Responsable magasin'],
        ['employee', 'Employé magasin'],
    ]
);

/* Contact role assigned table*/
$tableName = $installer->getTable('apdc_commercant/contact_role_assigned');
$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('contact_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['nullable' => false])
    ->addColumn('role_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['nullable' => false])
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/contact_role_assigned', 'contact_id', 'apdc_commercant/contact', 'id_contact'),
        'contact_id', $installer->getTable('apdc_commercant/contact'), 'id_contact'
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/contact_role_assigned', 'role_id', 'apdc_commercant/contact_role', 'role_id'),
        'role_id', $installer->getTable('apdc_commercant/contact_role'), 'role_id'
    )
;

$installer->getConnection()->createTable($table);
