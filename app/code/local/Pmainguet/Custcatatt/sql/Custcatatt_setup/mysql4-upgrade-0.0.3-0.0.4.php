    <?php
    $installer = $this;
    $installer->startSetup();

$attcomid  = array(
    'type'          =>  'int',
    'label'         =>  'ID option Attribut produits "Commerçant"',
    'input'         =>  'select',
    'source'        => 'custcatatt/attribute_commercant',
    'global'        =>  Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       =>  true,
    'required'      =>  false,
    'user_defined'  =>  true,
    'default'       =>  "",
    'group'         =>  "Infos Commerçants"
);

$installer->addAttribute('catalog_category', 'att_com_id', $attcomid);

$installer->endSetup();