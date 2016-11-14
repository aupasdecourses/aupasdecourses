<?php
$this->startSetup();

$menuBgColor = array(
	'group'         => 'Super Menu',
	'input_renderer'=> 'apdc_supermenu/adminhtml_catalog_category_form_element_color',
	'type'          => 'text',
	'label'         => 'Background color',
	'backend'       => '',
	'visible'       => true,
	'default'       => null,
	'required'      => false,
	'user_defined'  => true,
	'visible_on_front' => true,
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
);
$menuColor = array(
	'group'         => 'Super Menu',
	'input_renderer'=> 'apdc_supermenu/adminhtml_catalog_category_form_element_color',
	'type'          => 'text',
	'label'         => 'Text color',
	'backend'       => '',
	'visible'       => true,
	'default'       => null,
	'required'      => false,
	'user_defined'  => true,
	'visible_on_front' => true,
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
);

$menuTemplate = array(
	'group'         => 'Super Menu',
	'input'         => 'select',
	'type'          => 'text',
	'label'         => 'Template for sub-menu',
    'source'        => 'apdc_supermenu/adminhtml_system_config_source_template',
	'backend'       => '',
	'visible'       => true,
	'default'       => '',
	'required'      => false,
	'user_defined'  => true,
	'visible_on_front' => true,
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
);


$menuMainStaticBlock = array(
	'group'         => 'Super Menu',
	'input'         => 'select',
	'type'          => 'text',
	'label'         => 'Main static block',
    'note'          => 'Depend on menu template',
    'source'        => 'apdc_supermenu/adminhtml_system_config_source_staticBlocks',
	'backend'       => '',
	'visible'       => true,
	'default'       => '',
	'required'      => false,
	'user_defined'  => true,
	'visible_on_front' => true,
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
);

$menuStaticBlock1 = array(
	'group'         => 'Super Menu',
	'input'         => 'select',
	'type'          => 'text',
	'label'         => 'Static block 1',
    'note'          => 'Depend on menu template',
    'source'        => 'apdc_supermenu/adminhtml_system_config_source_staticBlocks',
	'backend'       => '',
	'visible'       => true,
	'default'       => '',
	'required'      => false,
	'user_defined'  => true,
	'visible_on_front' => true,
	'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
);

$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_bg_color', $menuBgColor);
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_text_color', $menuColor);
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_template', $menuTemplate);
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_main_static_block', $menuMainStaticBlock);
$this->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'menu_static_block1', $menuStaticBlock1);

$this->endSetup();
