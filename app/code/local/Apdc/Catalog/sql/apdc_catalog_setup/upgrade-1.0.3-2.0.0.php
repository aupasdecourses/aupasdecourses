<?php

/**
 * @category  Apdc
 * @package   Apdc_Neighborhood
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_catalog/product_availability'))

    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'primary' => true,
            'identity' => true,
            'nullable' => false,
        ),
        'Entity id'
    )
    ->addColumn(
        'product_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
        ),
        'Product ID'
    )
    ->addColumn(
        'website_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
        ),
        'Website Id'
    )
    ->addColumn(
        'delivery_date',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
            'nullable' => false
        ),
        'delivery date'
    )
    ->addColumn(
        'neighborhood_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
        ),
        'Neighborhood Id'
    )
    ->addColumn(
        'status',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
        ),
        'Product status : available or not + reason if not'
    )
    ->addIndex(
        $installer->getIdxName(
            'apdc_catalog/product_availability',
            array('product_id')
        ),
        array('product_id')
    )
    ->addIndex(
        $installer->getIdxName(
            'apdc_catalog/product_availability',
            array('website_id')
        ),
        array('website_id')
    )
    ->addIndex(
        $installer->getIdxName(
            'apdc_catalog/product_availability',
            array('neighborhood_id')
        ),
        array('neighborhood_id')
    )
    ->addIndex(
        $this->getIdxName(
            'apdc_catalog/product_availability',
            array('product_id', 'website_id', 'delivery_date')
        ),
        array('product_id', 'website_id', 'delivery_date'),
        array(
            'type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        )
    );
     


$installer->getConnection()->createTable($table);

$installer->endSetup();
