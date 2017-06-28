<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/commercant');

$installer->getConnection()->dropForeignKey(
    $tableName,
    $installer->getFkName('apdc_commercant/commercant', 'id_bank_information', 'apdc_commercant/bankInfo', 'id_bank_information')
);

