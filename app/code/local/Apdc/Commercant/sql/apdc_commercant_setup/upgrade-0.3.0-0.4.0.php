<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$tableName = $installer->getTable('apdc_commercant/shop');

$installer->run("ALTER TABLE $tableName CHANGE COLUMN closing_day closing_periods TEXT;");
