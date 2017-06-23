<?php
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$entity = $this->getEntityTypeId('customer');
$customerAttribute = array(
    'type' => 'int',
    'label' => 'My Neighborhood',
    'input' => 'select',
    'source' => 'apdc_neighborhood/source_option_neighborhood',
    'required' => true,
    'position' => 25,
    'visible' => 1,
    'user_defined' => true
);
$installer->addAttribute($entity, 'customer_neighborhood', $customerAttribute);

$attribute = Mage::getSingleton('eav/config')->getAttribute($entity, 'customer_neighborhood');
$attribute->setData('used_in_forms', array('customer_account_edit','customer_account_create','adminhtml_customer','checkout_register'));
$attribute->setSortOrder(25);
$attribute->save();

$installer->endSetup();
