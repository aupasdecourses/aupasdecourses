<?php

$installer = $this;
$installer->startSetup();

//Programmatically create Commercant customer group
$code = 'Commercants';

$collection = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_code', $code);
$customer_group = Mage::getModel('customer/group')->load($collection->getFirstItem()->getId());
$customer_group->setCode($code);
$customer_group->setTaxClassId(3);
$customer_group->save();

$installer->endSetup();