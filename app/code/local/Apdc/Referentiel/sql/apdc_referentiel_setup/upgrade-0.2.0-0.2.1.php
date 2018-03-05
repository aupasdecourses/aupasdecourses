<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('apdc_referentiel/referentiel');

if ($installer->tableExists($tableName)) {
    $installer->getConnection()->dropTable($tableName);
}
$table = $installer->getConnection()
    ->newTable($tableName)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, ['unsigned' => true, 'nullable' => false, 'primary' => true, 'auto_increment' => true], 'Entity Id')
    ->addColumn('type_ref', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, [], 'Type de référentiel')
    ->addColumn('code_taxonomie', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Code Taxonomie')
    ->addColumn('code_inter', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Code Inter')
    ->addColumn('name_inter', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Name Inter')
    ->addColumn('code_ref_apdc', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Code Référentiel APDC')
    ->addColumn('name_ref', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Name')
    ->addColumn('unite_prix', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, [], 'Prix Kilo Site')
    ->addColumn('short_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Short Description')
    ->addColumn('poids_portion', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, [], 'Poids Portion')
    ->addColumn('poids_unit', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, [], 'Poids Portion')
    ->addColumn('nbre_portion', Varien_Db_Ddl_Table::TYPE_SMALLINT, 10, [], 'Nombre Portion')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, ['unsigned' => true], 'Tax Class Id')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Image')
    ->addColumn('cat_parent', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Parent')
    ->addColumn('nom_cat', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Principale')
    ->addColumn('nom_sous_cat', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Sous Cat Principale')
    ->addColumn('nom_cat_boulanger', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Boulanger')
    ->addColumn('nom_cat_primeur', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Primeur')
    ->addColumn('nom_cat_fromager', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Fromager')
    ->addColumn('nom_cat_poissonnier', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Poissonnier')
    ->addColumn('nom_cat_traiteur', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Traiteur')
    ->addColumn('nom_cat_epicerie', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Epicerie')
    ->addColumn('nom_cat_type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Type')
    ->addColumn('nom_cat_bio', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Bio')
    ->addColumn('nom_cat_envie', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Cat Envie')
    ->addColumn('nom_sous_cat_envie', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Sous Cat Envie')					
    ->addColumn('maturite', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Option Maturité')
    ->addColumn('saisonnalite', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Saisonnalité')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, [], 'Description')
    ->addColumn('desc_internet', Varien_Db_Ddl_Table::TYPE_TEXT, null, [], 'Autre Description')
    ->addColumn('suggestion_utilisation', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Suggestion Utilisation')
    ->addColumn('conseil_commercant', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, [], 'Conseil Commerçant')
    ->addColumn('produit_fragile', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(),'Produit Fragile');
$installer->getConnection()->createTable($table);
$installer->endSetup();

