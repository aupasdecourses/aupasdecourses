<?php
$installer = $this;
$installer->startSetup();
$isclickable  = array(
    'group'         => 'General Information',
    'type'          =>  'int',
    'label'         =>  'Lien cliquable',
    'input'         =>  'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  1,
    'required'      =>  1,
    'default'       => '1'
);

//$installer->removeAttribute('catalog_category','livraison_commercant');
$installer->addAttribute('catalog_category', 'is_clickable', $isclickable);

$installer->endSetup();