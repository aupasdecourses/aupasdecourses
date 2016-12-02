<?php
$installer = $this;
$installer->startSetup();

//horaires & adresse

$horaires  = array(
    'type'          =>  'text',
    'label'         =>  'Horaires Commerçant',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$adresse  = array(
    'type'          =>  'text',
    'label'         =>  'Adresse Magasin',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$badge  = array(
    'type'          =>  'text',
    'label'         =>  'Badges',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$livraison  = array(
    'type'          =>  'text',
    'label'         =>  'Jour Livraison',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$estcom  = array(
    'type'          =>  'text',
    'label'         =>  'Est un commercant',
    'input'         =>  'select',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants",
    'option' => array ('value' => array(
    'optionone'=> array( 
        0 =>'Oui'),
    'optiontwo'=> array( 
        0 =>'Non')))
);

$installer->addAttribute('catalog_category', 'horaires_commercant', $horaires)
->addAttribute('catalog_category', 'adresse_commercant', $adresse)
->addAttribute('catalog_category', 'livraison_commercant', $livraison)
->addAttribute('catalog_category', 'estcom_commercant', $estcom)
->addAttribute('catalog_category', 'badge_commercant', $badge);

$installer->endSetup();
?>