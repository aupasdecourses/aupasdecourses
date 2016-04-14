<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2015 Amasty (https://www.amasty.com)
 * @package Amasty_Deliverydate
 */
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE `{$this->getTable('amdeliverydate/deliverydate')}`
ADD `reminder` TINYINT( 1 ) UNSIGNED NOT NULL ;

");

$installer->endSetup();