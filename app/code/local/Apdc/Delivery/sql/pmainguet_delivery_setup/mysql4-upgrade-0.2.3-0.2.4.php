<?php

// Add column "associated_order_id" in "indi_commenthistory table"
// For comments concerning two orders

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('pmainguet_delivery/indi_commenthistory');

$table = $installer->getConnection()
	->addColumn($tableName, 'associated_order_id', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		'nullable'	=> false,
		'default'	=> 0,
		'comment'	=> 'Deuxieme commande concernee'
	));

$installer->endSetup();