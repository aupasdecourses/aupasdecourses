<?php

$installer = $this;
$installer->startSetup();

//ajouter index sur la table référentiel

$tableName = $installer->getTable('pmainguet_delivery/refund_items');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName)) {
    $installer->getConnection()->addIndex(
        $installer->getTable('pmainguet_delivery/refund_items'),
        $installer->getIdxName('pmainguet_delivery/refund_items', array('order_item_id')),
        array('order_item_id')
    );
}
$installer->endSetup();

