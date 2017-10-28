<?php

/**
 * @category  Apdc
 * @package   Apdc_Neighborhood
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$installer->startSetup();

// Add new Attribute group
$groupName = 'Disponibilité';
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 7);
$attributeGroupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

$installer->addAttribute('catalog_product', 'availability_days', array(
    'group' => $groupName,
    'input' => 'multiselect',
    'type' => 'text',
    'label' => 'Disponibilité du produit',
    'backend' => 'eav/entity_attribute_backend_array',
    'source' => 'apdc_catalog/source_product_days',
    'visible' => 1,
    'required' => 1,
    'default' => '1,2,3,4,5,6,7',
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 1,
    'used_in_product_listing' => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'is_configurable' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));
$installer->addAttribute('catalog_product', 'can_order_days', array(
    'group' => $groupName,
    'input' => 'multiselect',
    'type' => 'text',
    'label' => 'Le produit peut être commandé',
    'backend' => 'eav/entity_attribute_backend_array',
    'source' => 'apdc_catalog/source_product_days',
    'visible' => 1,
    'required' => 1,
    'default' => '1,2,3,4,5,6,7',
    'user_defined' => 1,
    'searchable' => 0,
    'filterable' => 0,
    'comparable' => 0,
    'visible_on_front' => 1,
    'used_in_product_listing' => 1,
    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'is_configurable' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));

$installer->endSetup();
