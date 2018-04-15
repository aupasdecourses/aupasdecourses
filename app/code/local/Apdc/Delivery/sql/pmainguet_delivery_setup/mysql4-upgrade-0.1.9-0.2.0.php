<?php

$installer = $this;
$installer->startSetup();

// creation table indi_commenthistory

$table = $installer->getConnection()
	->newTable($installer->getTable('pmainguet_delivery/indi_commenthistory'))
	->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'primary' 			=> true, 
		'auto_increment' 	=> true, 
		'nullable' 			=> false,
		'comment'			=> 'ID',
	])
	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Date de creation',
	])
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Date de MAJ',
	])
	->addColumn('author', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Auteur',
	])
	->addColumn('comment_type', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Type',
	])
	->addColumn('comment_text', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
		'comment'	=> 'Contenu',
	])
	->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'nullable' 	=> false,
		'default' 	=> '0',
		'comment'	=> 'Numero de Commande',
	])
	->addColumn('merchant_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
		'nullable' 	=> false,
		'default' 	=> '0',
		'comment'	=> 'ID Attribut Commercant',
	])
	;

$installer->getConnection()->createTable($table);

$installer->endSetup();