<?php

$installer = $this; 

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/indi_billingdetails'), 'id_attribut_commercant', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		'nullable'	=> false,
		'default'	=> '0',
		'comment'	=> 'ID attribut commercant'
	));

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/indi_billingsummary'), 'id_attribut_commercant', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		'nullable'	=> false,
		'default'	=> '0',
		'comment'	=> 'ID attribut commercant'
	));

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/indi_billingsummary'), 'merchant_payout_status', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_TEXT,
		'length'	=> 255,
		'nullable'	=> true,
		'default'	=> null,
		'comment'	=> 'statut virement commercant'
	));

$installer->endSetup();
