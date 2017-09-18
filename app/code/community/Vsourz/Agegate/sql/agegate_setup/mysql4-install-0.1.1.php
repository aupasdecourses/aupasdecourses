<?php
$installer = Mage::getResourceModel('catalog/setup','catalog_setup');
$installer->startSetup();
$installer->addAttribute('catalog_product', 'show_age_popup', array(
'group' => 'General',
'input' => 'select',
'type' => 'int',
'label' => 'Show popup âge mini',
'visible' => 1,
'required' => 1,
'default' => '0',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
'source' => 'eav/entity_attribute_source_boolean',
));
$installer->endSetup();
