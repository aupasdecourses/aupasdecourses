<?php

$installer = $this;

$tableName = $installer->getTable('pmainguet_delivery/indi_billingdetails');

/*	TVA + frais_livraison_HT TVA & TTC dans billing_details */
$table = $installer->getConnection()
	->addColumn($tableName, 'sum_commission_TVA', array(
		'type'			=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'precision'		=> 12,
		'scale'			=> 2,
		'nullable'		=> false,
		'default'		=> '0.0000',
		'comment'		=> 'TVA',
	));
$table = $installer->getConnection()
	->addColumn($tableName, 'sum_shipping_HT', array(
		'type'			=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'precision'		=> 12,
		'scale'			=> 2,
		'nullable'		=> false,
		'default'		=> '0.0000',
		'comment'		=> 'Frais livraison HT',
	));
$table = $installer->getConnection()
	->addColumn($tableName, 'sum_shipping_TVA', array(
		'type'			=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'precision'		=> 12,
		'scale'			=> 2,
		'nullable'		=> false,
		'default'		=> '0.0000',
		'comment'		=> 'Frais livraison TVA',
	));
$table = $installer->getConnection()
	->addColumn($tableName, 'sum_shipping_TTC', array(
		'type'			=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
		'precision'		=> 12,
		'scale'			=> 2,
		'nullable'		=> false,
		'default'		=> '0.0000',
		'comment'		=> 'Frais livraison TTC',
	));

$summaryTable = $installer->getTable('pmainguet_delivery/indi_billingsummary');

/* Commentaires factu commercants */
$table = $installer->getConnection()
	->addColumn($summaryTable, 'merchant_bill_comment', array(
		'type'			=> Varien_Db_Ddl_Table::TYPE_TEXT,
		'length'		=> 255,
		'nullable'		=> true,
		'default'		=> null,
		'comment'		=> 'Commentaire commercant',
	));

$installer->endSetup();
