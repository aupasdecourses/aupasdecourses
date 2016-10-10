<?php
$installer = $this;
$installer->startSetup();
$googlesheetskey  = array(
    'type'          =>  'text',
    'label'         =>  'Google Sheets key',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);
$googlesheetsgid  = array(
    'type'          =>  'text',
    'label'         =>  'Google Sheets gid',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$installer->addAttribute('catalog_category', 'gs_key', $googlesheetskey);
$installer->addAttribute('catalog_category', 'gs_gid', $googlesheetsgid);

$installer->endSetup();