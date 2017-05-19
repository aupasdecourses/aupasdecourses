<?php

/**
 * @category  Apdc
 * @package   Apdc_Neighborhood
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_neighborhood/neighborhood'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Neighborhood ID'
    )
    ->addColumn(
        'is_active',
        Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        1,
        array(
            'nullable' => false
        ),
        'Neighborhood is active'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false
        ),
        'Neighborhood Name'
    )
    ->addColumn(
        'image',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false
        ),
        'Attribute Set Id'
    )
    ->addColumn(
        'website_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false,
        ),
        'Website ID'
    )
    ->addColumn(
        'sort_order',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'nullable' => false
        ),
        'Used to order messages'
    )
    ->addIndex(
        $installer->getIdxName(
            'apdc_neighborhood/neighborhood',
            array('website_id')
        ),
        array('website_id')
    )
    ->addForeignKey(
        $installer->getFkName(
            'apdc_neighborhood/neighborhood',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id',
        $installer->getTable('core/website'),
        'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
