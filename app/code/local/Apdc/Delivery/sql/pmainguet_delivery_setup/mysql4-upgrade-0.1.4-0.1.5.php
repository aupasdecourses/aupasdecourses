<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->newTable('indi_mistraldelivery')
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'primary' => true, 
		'auto_increment' => true, 
		'nullable' => false
	], 'ID')
	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'nullable' => false, 
		'default' => 0
	], 'Numero de commande')
	->addColumn('slot_start_picking', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [], 'Début supposé picking')
	->addColumn('slot_end_picking', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [], 'Fin supposée picking')
	->addColumn('real_hour_picking', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [], 'Date réelle picking')
	->addColumn('slot_start_shipping', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [], 'Début supposée shipping')
	->addColumn('slot_end_shipping', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [], 'Fin supposée shipping')
	->addColumn('real_hour_shipping', Varien_Db_Ddl_Table::TYPE_TEXT, 50, [], 'Date réelle shipping');

$installer->getConnection()->createTable($table);
$installer->endSetup();
