<?php
 
$installer = $this;
 
$installer->startSetup();
 
//Table Delivery Refunded Order
$table = $installer->getConnection()
    ->changeColumn($installer->getTable('pmainguet_delivery/refund_order'),'refund_order_id','id', array(
        'type'=>Varien_Db_Ddl_Table::TYPE_INTEGER, 
        'auto_increment' => true,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ))
    ->addColumn($installer->getTable('pmainguet_delivery/refund_order'),'creditmemo_id', array(
        'type'=>Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'default' => 0,
        'comment'=>'Credit Memo Id',
        ));
$installer->endSetup();
