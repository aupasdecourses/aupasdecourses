<?php

/**
 * @category  Apdc
 * @package   Apdc_Partner
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

// SALES ORDER INVOICE ITEMS
$installer->getConnection()->addColumn(
    $installer->getTable('sales/invoice_item'),
    'apdc_partner_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned' => true,
        'length'    => 11,
        'comment'   => 'Partner Id'
    )
);


$installer->endSetup();
