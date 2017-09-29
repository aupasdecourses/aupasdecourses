<?php  
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', 'risque_rupture', array(
      'group'                       => 'Infos Produits',
      'type'                        => 'int',
      'input'                       => 'select',
      'label'                       => 'Risque de rupture',
      'global'                      => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
      'visible'                     => 1,
      'required'                    => 0,
      'visible_on_front'            => 1,
      'is_configurable'             => 0,
      'source'                      => 'eav/entity_attribute_source_boolean',
      'unique'                      => false,
      'user_defined'                => true,
      'is_user_defined'             => true,
      'used_in_product_listing'     => true,
      'default'                     => 0,
      'apply_to'                    => 'simple,configurable,virtual',

));
$installer->endSetup();
?>

