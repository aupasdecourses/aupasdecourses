<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/* Commercant table */
$tableName = $installer->getTable('apdc_commercant/commercant');

$table = $installer->getConnection()->dropColumn($tableName, 'ceo_dob');


$tableName = $installer->getTable('apdc_commercant/contact');
$table = $installer->getConnection()
    ->addColumn($tableName, 'dob', ['type' => Varien_Db_Ddl_Table::TYPE_DATE, 'comment' => 'Date of birth']);