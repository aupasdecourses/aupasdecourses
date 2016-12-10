<?php

$installer = new Mage_Sales_Model_Mysql4_Setup('core_setup');

//Remove uneeded item attribute in non-item related table
$entities = array(
    'pmainguet_delivery/refund_items' => array('commercant_id','order_id'),
    'pmainguet_delivery/refund_order' => array('commercant_id','final_row_total','del_tax_refunded'),
);

foreach ($entities as $table => $entity) {
    foreach ($entity as $option) {
        $installer->getConnection()->dropColumn($installer->getTable($table), $option);
    }
}