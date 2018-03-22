<?php

$installer = $this;
$installer->startSetup();

// creation table indi_commenttype

$table = $installer->getConnection()
	->newTable($installer->getTable('pmainguet_delivery/indi_commenttype'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'primary' 			=> true, 
		'auto_increment' 	=> true, 
		'nullable' 			=> false,
		'comment'			=> 'ID',
	])
	->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Type',
	])
	->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Label'
	])
	;

$installer->getConnection()->createTable($table);

$installer->endSetup();