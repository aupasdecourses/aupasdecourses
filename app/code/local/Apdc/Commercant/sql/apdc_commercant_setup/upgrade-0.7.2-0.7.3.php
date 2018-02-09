<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$tableName = $installer->getTable('apdc_commercant/shop');

$installer->getConnection()->addColumn(
    $tableName,
    'shop_type',
    [
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'after' => 'id_contact_employee_bis',
        'length' => 200,
        'default' => '',
        'comment' => 'Libelle catégorie principale'
    ]
);

$shopCategoriesTable = $installer->getTable('apdc_commercant/shop_categories');
$shops = $installer->getConnection()->select()
    ->from(['main_table' => $shopCategoriesTable])
    ->order('category_id ASC');
$shops->join(
    ['cat' => $installer->getTable('catalog/category')],
    'cat.entity_id = main_table.category_id',
    array()
);
$shops->where('cat.level = 3');
$catByShopId = [];
foreach ($installer->getConnection()->fetchAll($shops) as $shop) {
    if (!isset($catByShopId[$shop['shop_id']])) {
        $cat = Mage::getModel('catalog/category')->load($shop['category_id']);
        $catByShopId[$shop['shop_id']] = $cat->getParentCategory()->getName();
    }
}
$shopTable = $installer->getTable('apdc_commercant/shop');
foreach ($catByShopId as $shopId => $catName) {
    $installer->getConnection()->update(
        $shopTable,
        ['shop_type' => $catName],
        'id_shop = ' . (int) $shopId
    );
}

