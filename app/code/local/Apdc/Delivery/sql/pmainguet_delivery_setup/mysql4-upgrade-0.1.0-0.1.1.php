<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/* Contact table */
$tableName = $installer->getTable('pmainguet_delivery/indi_billingsummary');

$table = $installer->getConnection()
	->addColumn($tableName,'sum_virement', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'precision'     => 12,
        'scale' => 2,
		'nullable'	=> false,
		'default'	=> '0.0000',
		'comment'	=> 'Sum Virement Hipay',
	));

$installer->endSetup();