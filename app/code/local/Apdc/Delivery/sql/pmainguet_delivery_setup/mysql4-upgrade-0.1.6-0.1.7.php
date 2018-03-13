<?php

$installer = $this;
$installer->startSetup();

// creation table refund_pricevariation
$table = $installer->getConnection()
	->newTable($installer->getTable('pmainguet_delivery/refund_pricevariation'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'primary' 			=> true, 
		'auto_increment' 	=> true, 
		'nullable' 			=> false,
		'comment'			=> 'ID',
	])
	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'nullable' 	=> false,
		'default' 	=> '0',
		'comment'	=> 'Numero de Commande',
	])
	->addColumn('merchant', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Commercant',
	])
	->addColumn('merchant_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'nullable' 	=> false,
		'default' 	=> '0',
		'comment'	=> 'ID commercant',
	])
	->addColumn('merchant_excess', Varien_Db_Ddl_Table::TYPE_FLOAT, null, [
		'nullable' 	=> true,
		'default' 	=> '0.00',
		'comment'	=> 'Excedent commercant',
	])
	->addColumn('merchant_lack', Varien_Db_Ddl_Table::TYPE_FLOAT, null, [
		'nullable' 	=> true,
		'default' 	=> '0.00',
		'comment'	=> 'Manque commercant',
	]);

$installer->getConnection()->createTable($table);

$installer->endSetup();