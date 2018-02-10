<?php

$installer = $this;

$tableName = $installer->getTable('apdc_commercant/shop');
$table = $installer->getConnection()->dropColumn($tableName, 'type_shop');

$installer->getConnection()->dropTable('apdc_typeshop');
