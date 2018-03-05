<?php

/**
 * @category  Apdc
 * @package   Apdc_Partner
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_partner/partner'))
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
        'Partner ID'
    )
    ->addColumn(
        'is_active',
        Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        1,
        array(
            'nullable' => false
        ),
        'Partner is active'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false
        ),
        'Partner Name'
    )
    ->addColumn(
        'email',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false
        ),
        'Partner Email'
    )
    ->addColumn(
        'partner_key',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false
        ),
        'Partner Key'
    )
    ->addColumn(
        'partner_secret',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        255,
        array(
            'nullable' => false
        ),
        'Partner Secret'
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
