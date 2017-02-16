<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$attributes = array(
    'delivery_days',
    'gs_gid',
    'gs_key'
);
foreach ($attributes as $attribute) {
    $installer->removeAttribute('catalog_category', $attribute);
}