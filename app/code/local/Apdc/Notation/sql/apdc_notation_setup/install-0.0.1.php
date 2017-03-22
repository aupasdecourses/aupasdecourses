<?php
 
$installer = $this;
 
$installer->startSetup();
 
$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_notation/notation'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Note Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Order Item Id')
    ->addColumn('note', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
        ), 'Note');
$installer->getConnection()->createTable($table);

$installer->endSetup();