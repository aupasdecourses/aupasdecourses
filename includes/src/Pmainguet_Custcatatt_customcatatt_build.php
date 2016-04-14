    <?php

    require_once "app/Mage.php";

    Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));


    $installer = new Mage_Sales_Model_Mysql4_Setup;
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
    'group'         =>  "General Information",
    'option' => array ('value' => array(
    'optionone'=> array( 
        0 =>'Oui'),
    'optiontwo'=> array( 
        0 =>'Non')))
);

$infocomp  = array(
    'type'          =>  'text',
    'label'         =>  'Infos Complémentaires',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$telephone  = array(
    'type'          =>  'text',
    'label'         =>  'Téléphone',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$portable  = array(
    'type'          =>  'text',
    'label'         =>  'Portable',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$siteinternet  = array(
    'type'          =>  'text',
    'label'         =>  'Site Internet',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$mailpro  = array(
    'type'          =>  'text',
    'label'         =>  'Mail Pro',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$mailapdc  = array(
    'type'          =>  'text',
    'label'         =>  'Mail APDC',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$installer->addAttribute('catalog_category', 'mail_pro', $mailpro)
->addAttribute('catalog_category', 'mail_apdc', $mailapdc);
// ->addAttribute('catalog_category', 'portable', $portable)
// ->addAttribute('catalog_category', 'site_internet', $siteinternet);
//->addAttribute('catalog_category', 'horaires_commercant', $horaires)
//->addAttribute('catalog_category', 'adresse_commercant', $adresse)
//->addAttribute('catalog_category', 'livraison_commercant', $livraison)
//->addAttribute('catalog_category', 'estcom_commercant', $estcom)
//->addAttribute('catalog_category', 'badge_commercant', $badge);

$installer->endSetup();