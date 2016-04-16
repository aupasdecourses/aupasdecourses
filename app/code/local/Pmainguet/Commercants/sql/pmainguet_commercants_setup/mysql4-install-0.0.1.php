<?php

$installer = $this;
$installer->startSetup();

//Programmatically create Commercant customer group
$code = 'Commercants';
$customer_group=Mage::getModel('customer/group');
$customer_group->setCode($code);
$customer_group->setTaxClassId(3);
$customer_group->save();

$installer->endSetup();