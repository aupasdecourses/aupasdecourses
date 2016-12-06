<?php
$installer = Mage::getResourceModel('catalog/setup','catalog_setup');
$installer->startSetup();
$show_age_popup  = array(
    'group'         => 'General Information',
    'type'          =>  'int',
    'label'         =>  'Show âge mini popup',
    'input'         =>  'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  1,
    'required'      =>  1,
    'default'       => '0'
);
$installer->addAttribute('catalog_category', 'show_age_popup', $show_age_popup);
$installer->endSetup();
