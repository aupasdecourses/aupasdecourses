<?php

/**
 * @category  Apdc
 * @package   Apdc_Partner
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

// QUOTE
$connection = $installer->getConnection();
$connection->addColumn(
    $installer->getTable('sales/quote'),
    'apdc_partner_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned' => true,
        'length'    => 11,
        'comment'   => 'Partner Id'
    )
);
$connection->addColumn(
    $installer->getTable('sales/quote'),
    'apdc_partner_request',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => '16k',
        'comment'   => 'Partner request for quote creation'
    )
);

// QUOTE ITEMS
$connection->addColumn(
    $installer->getTable('sales/quote_item'),
    'apdc_partner_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned' => true,
        'length'    => 11,
        'comment'   => 'Partner Id'
    )
);


// SALES ORDER
$connection->addColumn(
    $installer->getTable('sales/order'),
    'apdc_partner_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned' => true,
        'length'    => 11,
        'comment'   => 'Partner Id'
    )
);
$connection->addColumn(
    $installer->getTable('sales/order'),
    'apdc_partner_request',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => '16k',
        'comment'   => 'Partner request for quote creation'
    )
);


// SALES ORDER ITEMS
$connection->addColumn(
    $installer->getTable('sales/order_item'),
    'apdc_partner_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned' => true,
        'length'    => 11,
        'comment'   => 'Partner Id'
    )
);


// SALES ORDER INVOICE
$connection->addColumn(
    $installer->getTable('sales/invoice'),
    'apdc_partner_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'unsigned' => true,
        'length'    => 11,
        'comment'   => 'Partner Id'
    )
);
$connection->addColumn(
    $installer->getTable('sales/invoice'),
    'apdc_partner_request',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => false,
        'length'    => '16k',
        'comment'   => 'Partner request for quote creation'
    )
);
$installer->endSetup();

