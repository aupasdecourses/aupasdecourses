<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;


/* Contact table */
$tableName = $installer->getTable('apdc_commercant/contact');

if ($installer->tableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('id_contact', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false])
    ->addColumn('lastname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('firstname', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_INTEGER)
;
$installer->getConnection()->createTable($table);

/* Bank information table */
$tableName = $installer->getTable('apdc_commercant/bankInfo');

if ($installer->tableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('id_bank_information', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false])
    ->addColumn('owner_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('id_card', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('bank_account', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('kbis', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('account_iban', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('account_bic', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('bank_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('bank_street', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('bank_postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('bank_city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('bank_country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5)
;
$installer->getConnection()->createTable($table);

/* Commercant table */
$tableName = $installer->getTable('apdc_commercant/commercant');

if ($installer->tableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('id_commercant', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false])
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('hq_street', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('hq_postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('hq_city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('hq_country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5)
    ->addColumn('siren', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('hq_siret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('vat_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('id_contact_ceo', Varien_Db_Ddl_Table::TYPE_INTEGER, ['nullable' => false])
    ->addColumn('ceo_dob', Varien_Db_Ddl_Table::TYPE_DATE, ['nullable' => false])
    ->addColumn('id_contact_billing', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addColumn('id_bank_information', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/commercant', 'id_contact_ceo', 'apdc_commercant/contact', 'id_contact'),
        'id_contact_ceo', $installer->getTable('apdc_commercant/contact'), 'id_contact'
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/commercant', 'id_contact_billing', 'apdc_commercant/contact', 'id_contact'),
        'id_contact_billing', $installer->getTable('apdc_commercant/contact'), 'id_contact'
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/commercant', 'id_bank_information', 'apdc_commercant/bankInfo', 'id_bank_information'),
        'id_bank_information', $installer->getTable('apdc_commercant/bankInfo'), 'id_bank_information'
    )
;
$installer->getConnection()->createTable($table);

/* Shop table */
$tableName = $installer->getTable('apdc_commercant/shop');

if ($installer->tableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}

$table = $installer->getConnection()->newTable($tableName);
$table
    ->addColumn('id_shop', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false])
    ->addColumn('id_commercant', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['nullable' => false])
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('street', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('siret', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('phone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 15)
    ->addColumn('website', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('timetable', Varien_Db_Ddl_Table::TYPE_TEXT)
    ->addColumn('closing_day', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('vat_number', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('google_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('google_key', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255)
    ->addColumn('id_contact_manager', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addColumn('id_contact_employee', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addColumn('id_category', Varien_Db_Ddl_Table::TYPE_INTEGER)
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/shop', 'id_commercant', 'apdc_commercant/commercant', 'id_commercant'),
        'id_commercant', $installer->getTable('apdc_commercant/commercant'), 'id_commercant'
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/shop', 'id_contact_manager', 'apdc_commercant/contact', 'id_contact'),
        'id_contact_manager', $installer->getTable('apdc_commercant/contact'), 'id_contact'
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/shop', 'id_contact_employee', 'apdc_commercant/contact', 'id_contact'),
        'id_contact_employee', $installer->getTable('apdc_commercant/contact'), 'id_contact'
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/shop', 'id_category', 'catalog/category', 'entity_id'),
        'id_category', $installer->getTable('catalog/category'), 'entity_id'
    )
;
$installer->getConnection()->createTable($table);
