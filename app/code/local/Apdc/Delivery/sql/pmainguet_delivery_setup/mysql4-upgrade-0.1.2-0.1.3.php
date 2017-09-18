<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/* Contact table */
$tableName = $installer->getTable('pmainguet_delivery/indi_billingsummary');

$table = $installer->getConnection()
	->addColumn($installer->getTable('amasty_amorderattach_order_field'), 'closure', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_TEXT,
		'length'	=> 255,
		'nullable'	=> true,
		'default'	=> null,
		'comment'	=> ' closure status'
	));

$installer->endSetup();