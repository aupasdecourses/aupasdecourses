<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_referentiel/referentiel'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true,'nullable'  => false,'primary'=>true,'auto_increment'=>true), 'Entity Id')
    ->addColumn('type_ref', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(), 'Type de référentiel')
    ->addColumn('code_taxonomie', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Code Taxonomie')
    ->addColumn('code_ref_apdc', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Code Référentiel APDC')
    ->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Name')
    ->addColumn('type1',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Type 1')
    ->addColumn('type2',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Type 2')
    ->addColumn('type3',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Type 3')
    ->addColumn('brand',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Brand')
    ->addColumn('denomination',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Denomination')
    ->addColumn('unite_prix', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(),'Prix Kilo Site')
    ->addColumn('short_description',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Short Description')
    ->addColumn('poids_portion',Varien_Db_Ddl_Table::TYPE_VARCHAR,64,array(),'Poids Portion')
    ->addColumn('poids_unit',Varien_Db_Ddl_Table::TYPE_VARCHAR,64,array(),'Poids Portion')
    ->addColumn('nbre_portion',Varien_Db_Ddl_Table::TYPE_SMALLINT,10,array(),'Nombre Portion')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true), 'Tax Class Id')
    ->addColumn('image',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Image')
    ->addColumn('cat_principale',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Cat Principale')
    ->addColumn('sous_cat',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Sous Cat')
    ->addColumn('cat_traiteur',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Cat Traiteur')
    ->addColumn('cat_epicerie',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Cat Epicerie')
    ->addColumn('type_prepa',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Option Type prepa')
    ->addColumn('maturite',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Option Maturité')
    ->addColumn('saisonnalite',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Saisonnalité')
    ->addColumn('description',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Description')
    ->addColumn('desc_internet',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Autre Description')
    ->addColumn('suggestion_utilisation', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Suggestion Utilisation')
    ->addColumn('conseil_commercant', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Conseil Commerçant');
$installer->getConnection()->createTable($table);
$installer->endSetup();
