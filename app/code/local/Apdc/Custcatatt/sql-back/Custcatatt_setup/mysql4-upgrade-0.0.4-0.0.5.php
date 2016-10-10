<?php
$installer = $this;
$installer->startSetup();
$mail3  = array(
    'type'          =>  'text',
    'label'         =>  'Mail n°3',
    'input'         =>  'text',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$installer->addAttribute('catalog_category', 'mail_3', $mail3);

$installer->endSetup();
