<?php

$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

$entities = array(
	'refund',
);

foreach ($entities as $entity) {
    $installer->getConnection()->addColumn($installer->getTable('amasty_amorderattach_order_field'), $entity, array(
	    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
	    'length' => 255,
	    'nullable' => true,
	    'default' => null,
	    'comment' => $entity.' status'
	));
}