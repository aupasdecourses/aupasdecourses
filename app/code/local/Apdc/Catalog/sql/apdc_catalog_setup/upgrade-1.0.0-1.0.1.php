<?php
$this->startSetup();
$this->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'has_options', 'used_in_product_listing', '1');

$this->endSetup();
