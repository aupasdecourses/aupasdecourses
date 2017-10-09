<?php

/**
 * @category  Apdc
 * @package   Apdc_Neighborhood
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->dropColumn(
        $installer->getTable('apdc_neighborhood/neighborhood'),
        'opening_days'
    );

$installer->endSetup();
