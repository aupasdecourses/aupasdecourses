<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

/* Contact table */
$tableName = $installer->getTable('pmainguet_delivery/indi_billingdetails');

if (!$installer->tableExists($tableName)) {
    //$installer->getConnection()->dropTable($tableName);
  $table = $installer->getConnection()->newTable($tableName);
    $table
         ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false], 'Id Key')
         ->addColumn('order_shop_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Order Shop Id')
         ->addColumn('creation_date', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          ), 'Created on')
         ->addColumn('delivery_date', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          ), 'Delivered on')
         ->addColumn('billing_month', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          ), 'Billing month')
         ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          ), 'Increment Id')
         ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
          ), 'Customer Name')
         ->addColumn('shop_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
          'nullable' => false,
          'default' => '0',
          ), 'Id Shop')
         ->addColumn('shop', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
          ), 'Commercant Name')
         ->addColumn('id_billing', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          ), 'Billing Id')
         ->addColumn('sum_items_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Items HT')
          ->addColumn('sum_items', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum items TTC')
         ->addColumn('sum_items_credit_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Items Credit HT')
         ->addColumn('sum_items_credit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Items Credit TTC')
         ->addColumn('sum_ticket_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Ticket HT')
         ->addColumn('sum_ticket', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Ticket TTC')
         ->addColumn('sum_commission_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum commission HT')
         ->addColumn('sum_due_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum due HT');

    $installer->getConnection()->createTable($table);
}

/* Contact table */
$tableName = $installer->getTable('pmainguet_delivery/indi_billingsummary');

if (!$installer->tableExists($tableName)) {
    //$installer->getConnection()->dropTable($tableName);

  $table = $installer->getConnection()->newTable($tableName);
    $table
         ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, ['primary' => true, 'auto_increment' => true, 'nullable' => false], 'Id Key')
          ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Increment Id')
          ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(69), 'Created At')
          ->addColumn('date_finalized', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(69), 'Date finalized')
          ->addColumn('date_payout', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(69), 'Date Payout')
          ->addColumn('date_sent', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(69), 'Date sent')
          ->addColumn('billing_month', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
          ), 'Billing month')
         ->addColumn('shop_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
          'nullable' => false,
          'default' => '0',
          ), 'Id Shop')
         ->addColumn('shop', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
          ), 'Commercant Name')
         ->addColumn('sum_items_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Items HT')
         ->addColumn('sum_items', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum items TTC')
         ->addColumn('sum_items_credit_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Items Credit HT')
         ->addColumn('sum_items_credit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Items Credit TTC')
         ->addColumn('sum_ticket_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Ticket HT')
         ->addColumn('sum_ticket', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Ticket TTC')
         ->addColumn('sum_commission_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum commission HT')
          ->addColumn('sum_commission_TVA_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'TVA commission %')
          ->addColumn('sum_commission_TVA', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum TVA commission')
          ->addColumn('sum_commission', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum commission TTC')
         ->addColumn('sum_due_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Versement HT')
          ->addColumn('sum_due', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum Due TTC')
         ->addColumn('discount_shop_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Discount Shop HT')
         ->addColumn('discount_shop_TVA_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'TVA discount shop %')
          ->addColumn('discount_shop_TVA', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'TVA Discount shop')
         ->addColumn('discount_shop', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Discount Shop TTC')
         ->addColumn('comments_discount_shop', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
          'nullable' => false,
          'default' => 'NA',
          ), 'Commentaires Discount')
         ->addColumn('processing_fees_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Processing Fees HT')
         ->addColumn('processing_fees_TVA_percent', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'TVA processing_fees %')
          ->addColumn('processing_fees_TVA', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'TVA processing_fees')
         ->addColumn('processing_fees', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Processing Fees TTC')
         ->addColumn('sum_billing_HT', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum payout HT')
          ->addColumn('sum_billing_TVA', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'TVA sum_billing')
         ->addColumn('sum_billing', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum payout TTC')
         ->addColumn('sum_payout', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,2', array(
          'nullable' => false,
          'default' => '0.0000',
          ), 'Sum payout TTC');

    $installer->getConnection()->createTable($table);
}

$installer->addEntityType('billing', array(
    'entity_model' => 'pmainguet_delivery/indi_billingsummary',
    'table' => 'pmainguet_delivery/indi_billingsummary',
    'increment_model' => 'pmainguet_delivery/entity_increment',
    'increment_per_store' => false,
    'increment_pad_length' => 6,
    'increment_pad_char' => 0,
));

$installer->endSetup();
