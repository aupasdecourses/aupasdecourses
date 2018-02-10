<?php
/**
 * @category  Apdc
 * @package   Apdc_Commercant
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

// CREATE NEW TABLE
$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_commercant/shop_categories'))
    ->addColumn(
        'shop_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ],
        'Commercant Shop ID'
    )
    ->addColumn(
        'category_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        [
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ],
        'Category Id'
    )
    ->addIndex(
        $installer->getIdxName('apdc_commercant/shop_categories', array('category_id')),
        array('category_id')
    )
    ->addIndex(
        $installer->getIdxName('apdc_commercant/shop_categories', array('shop_id')),
        array('shop_id')
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/shop_categories', 'shop_id', 'apdc_commercant/shop', 'id_shop'),
        'shop_id',
        $installer->getTable('apdc_commercant/shop'),
        'id_shop',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $installer->getFkName('apdc_commercant/shop_categories', 'category_id', 'catalog/category', 'entity_id'),
        'category_id',
        $installer->getTable('catalog/category'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Commercant Shop to Category Linkage Table');
$installer->getConnection()->createTable($table);


$shopTable = $installer->getTable('apdc_commercant/shop');

// GET DATA ID_CATEGORY FROM APDC_SHOP TO APDC_SHOP_CATEGORIES
$shops = $installer->getConnection()->select()
    ->from($shopTable, ['id_shop', 'id_category']);

$shopCategoriesTable = $installer->getTable('apdc_commercant/shop_categories');
foreach ($installer->getConnection()->fetchAll($shops) as $shop) {
    $insert = explode(',', $shop['id_category']);
    $data = [];
    foreach ($insert as $categoryId) {
        $data[] = array(
            'shop_id'  => (int) $shop['id_shop'],
            'category_id' => (int) $categoryId
        );
    }

    $installer->getConnection()->insertMultiple($shopCategoriesTable, $data);
}


// REMOVE OLD COLMUN
$installer->getConnection()->dropColumn($shopTable, 'id_category');
$installer->endSetup();
