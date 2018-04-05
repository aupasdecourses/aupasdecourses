<?php

/**
 * @category  Apdc
 * @package   Apdc_Partner
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$installer = $this;
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();
 
// the attribute added will be displayed under the group/tab Special Attributes in product edit page
$setup->updateAttribute(
    'catalog_product',
    'produit_biologique',
    [
        'source_model' => 'eav/entity_attribute_source_table'
    ]
);
 
$installer->endSetup();
