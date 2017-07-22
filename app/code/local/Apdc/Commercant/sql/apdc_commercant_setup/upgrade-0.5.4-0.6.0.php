<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$tableName = $installer->getTable('apdc_commercant/shop');


$installer->getConnection()->addColumn(
    $tableName,
    'category_image',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'after' => 'id_category',
        'comment' => 'Category Image'
    ]
);
$installer->getConnection()->addColumn(
    $tableName,
    'category_thumbnail',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'after' => 'category_image',
        'comment' => 'Category Thumbnail'
    ]
);
$installer->getConnection()->addColumn(
    $tableName,
    'category_meta_title',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 255,
        'after' => 'category_thumbnail',
        'comment' => 'Category Meta Title'
    ]
);
$installer->getConnection()->addColumn(
    $tableName,
    'category_meta_description',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '32k',
        'after' => 'category_meta_title',
        'comment' => 'Category Meta Description'
    ]
);
$installer->getConnection()->addColumn(
    $tableName,
    'category_description',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '64k',
        'after' => 'category_meta_description',
        'comment' => 'Category Description'
    ]
);
