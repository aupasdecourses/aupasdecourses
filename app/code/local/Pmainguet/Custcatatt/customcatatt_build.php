   <?php

    require_once "app/Mage.php";

    Mage::app()->setCurrentStore(Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID));


    $installer = new Mage_Sales_Model_Mysql4_Setup;
$installer->startSetup();

$attcomid  = array(
    'type'          =>  'integer',
    'label'         =>  'ID option Attribut produits "Commerçant"',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$installer->addAttribute('catalog_category', 'att_com_id', $attcomid);

$installer->endSetup();