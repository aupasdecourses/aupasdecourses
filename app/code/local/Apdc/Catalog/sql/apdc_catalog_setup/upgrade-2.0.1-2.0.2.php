<?php  
$installer = new Mage_Eav_Model_Entity_Setup('core_setup');
$installer->startSetup();

$array=array('Nom Catégorie Boucher'=>'nom_cat_boucher','Nom Catégorie Boulanger'=>'nom_cat_boulanger','Nom Catégorie Primeur'=>'nom_cat_primeur','Nom Catégorie Fromager'=>'nom_cat_fromager','Nom Catégorie Poissonnier'=>'nom_cat_poissonnier','Nom Catégorie Bio'=>'nom_cat_bio');

foreach($array as $label => $value){
      $installer->addAttribute('catalog_product', $value, array(
                  'group'           => 'Infos Produits',
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
      ));
}

$installer->endSetup();
?>