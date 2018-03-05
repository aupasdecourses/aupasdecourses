<?php  
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

//Create New Group
$groupName = 'Infos Produits - Vin';
$entityTypeId = $installer->getEntityTypeId('catalog_product');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 85);
$attributeGroupId = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

//Create New Attributes in New Group
$array=array(
      'Vin - Type'=>'vin_type',
      'Vin - Couleur'=>'vin_couleur',
      'Vin - Domaine'=>'vin_region',
      'Vin - Appellation'=>'vin_appellation',
      'Vin - Domaine'=>'vin_domaine',
      'Vin - Nom du Vigneron'=>'vin_vigneron',
      'Vin - Dénomination'=>'vin_denomination',
      'Vin - Millésime'=>'vin_millesime',
);

$counter=0;

foreach($array as $label => $value){
      $installer->addAttribute('catalog_product', $value, array(
                  'group'           => $groupName,
                  'label'           => $label,
                  'input'           => 'text',
                  'type'            => 'varchar',
                  'required'        => 0,
                  'visible_on_front'=> 0,
                  'filterable'      => 0,
                  'searchable'      => 0,
                  'comparable'      => 0,
                  'user_defined'    => 1,
                  'is_configurable' => 0,
                  'global'          => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                  'note'            => '',
                  'sort_order' => $counter,
      ));
      $counter+=10;
}

//Update Attribute

$array=array(
      'caracteristiques'=>'Vin - Caractéristiques',
      'composition'=>'Vin - Cépage',
      'conditionnement'=>'Vin - Conditionnement'
);

foreach($array as $value => $label){
      $installer->updateAttribute('catalog_product', $value, 'group',$groupName);
      $installer->updateAttribute('catalog_product', $value, 'label',$label);
}

$installer->endSetup();
?>