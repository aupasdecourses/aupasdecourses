<?php

/**
 * @category  Apdc
 * @package   Apdc_Neighborhood
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();
$connection->dropColumn(
    $installer->getTable('apdc_neighborhood/neighborhood'),
    'is_active'
);
$connection->dropColumn(
    $installer->getTable('apdc_neighborhood/neighborhood'),
    'name'
);
$connection->dropColumn(
    $installer->getTable('apdc_neighborhood/neighborhood'),
    'sort_order'
);
$connection->dropColumn(
    $installer->getTable('apdc_neighborhood/neighborhood'),
    'image'
);
$connection->dropColumn(
    $installer->getTable('apdc_neighborhood/neighborhood'),
    'image_banner'
);
$connection->dropColumn(
    $installer->getTable('apdc_neighborhood/neighborhood'),
    'postcodes'
);

$installer->endSetup();
