<?php
$installer = $this;
$installer->startSetup();
$delivery_days  = array(
    'label'         =>  'Jours de Livraison',
    'type'          =>  'varchar',
    'input'         =>  'multiselect',
    'backend' => 'eav/entity_attribute_backend_array',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'option' => array (
        'values' => array(
            '1' => 'Lundi',
            '2' => 'Mardi',
            '3' => 'Mercredi',
            '4' => 'Jeudi',
            '5' => 'Vendredi',
            '6' => 'Samedi',
            '7' => 'Dimanche',
        )),
    'visible_on_front' => true,
    'visible_in_advanced_search' => false,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants",
);

//$installer->removeAttribute('catalog_category','livraison_commercant');
$installer->addAttribute('catalog_category', 'delivery_days', $delivery_days);

$installer->endSetup();