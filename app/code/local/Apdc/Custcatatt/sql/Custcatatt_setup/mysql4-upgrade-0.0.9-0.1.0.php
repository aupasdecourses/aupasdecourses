<?php
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$attributes = array(
    'adresse_commercant',
    'horaires_commercant',
    'badge_commercant',
    'infos_complementaires',
    'telephone',
    'portable',
    'site_internet',
    'nom_contact',
    'mail_apdc',
    'att_com_id',
);
foreach ($attributes as $attribute) {
    $installer->removeAttribute('catalog_category', $attribute);
}