<?php
$this->startSetup();

$showIn = array(
	'group'         => 'General Information',
	'input'         => 'select',
	'type'          => 'int',
	'label'         => 'Affichage dans le menu',
    'source'        => 'apdc_supermenu/adminhtml_system_config_source_includeInMenu',
	'backend'       => '',
	'visible'       => true,
	'default'       => '1',
	'required'      => false,
	'user_defined'  => true,
	'visible_on_front' => true,
    'note'          => 'Pris en compte uniquement si la valeur du champ "Include in Navigation Menu" est à Oui',
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
);



$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'show_in_navigation', $showIn);

$this->endSetup();
