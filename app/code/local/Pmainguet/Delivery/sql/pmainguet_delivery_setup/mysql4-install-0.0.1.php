<?php
 
$installer = $this;
 
$installer->startSetup();
 
//Table Delivery Refunded Items
$table = $installer->getConnection()
    ->newTable($installer->getTable('pmainguet_delivery/refund_items'))
    ->addColumn('refund_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Refund Item Id')
    ->addColumn('order_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default' => NULL,
        ), 'Order Item Id')
    ->addColumn('item_name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Item name')
    ->addColumn('commercant', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Commercant')
    ->addColumn('commercant_id', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Commercant Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
        ), 'Order Id')
    ->addColumn('in_ticket', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'default'  => false,
        ), 'In Ticket')
    ->addColumn('prix_initial', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => 0.0000,
        ), 'Prix initial')
    ->addColumn('prix_final', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => 0.0000,
        ), 'Prix final')
    ->addColumn('diffprixfinal', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => 0.0000,
        ), 'Différence prix')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Comment');
$installer->getConnection()->createTable($table);

//Table Delivery Refunded Order
$table = $installer->getConnection()
    ->newTable($installer->getTable('pmainguet_delivery/refund_order'))
    ->addColumn('refund_order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Refund Order Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
        ), 'Order Id')
    ->addColumn('commercant', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Commercant')
    ->addColumn('commercant_id', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        ), 'Commercant Id')
    ->addColumn('final_row_total', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => 0.0000,
        ), 'Row Total')
    ->addColumn('del_amount_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => 0.0000,
        ), 'Delivery Amount Refunded')
    ->addColumn('del_tax_refunded', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'default'   => 0.0000,
        ), 'Delivery Tax Refunded')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => false,
        ), 'Comment');
$installer->getConnection()->createTable($table);
$installer->endSetup();