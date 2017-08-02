<?php

/**
 * @category  Apdc
 * @package   Apdc_Neighborhood
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();
$tablename=$installer->getTable('apdc_neighborhood/neighborhood');

$installer->getConnection()->addColumn(
        $tablename,
        'image_banner',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'comment'   => 'Neighborhood Banner'
        )
    );
$installer->getConnection()->addColumn(
        $tablename,
        'mistral_guid',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'comment'   => 'MISTRAL GUID (API)'
        )
    );

$installer->endSetup();
