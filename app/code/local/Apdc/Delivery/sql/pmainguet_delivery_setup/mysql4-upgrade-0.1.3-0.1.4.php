<?php

$installer = $this;

/*	Add tva in billing_details
	TVA == sum_commission_HT * 0.2 */
$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/indi_billingdetails'), 'sum_commission_TVA', array(
		'type'			=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'precision'		=> 12,
		'scale'			=> 2,
		'nullable'		=> false,
		'default'		=> '0.0000',
		'comment'		=> 'sum_commission_HT * 0.2',
	));

$installer->endSetup();
