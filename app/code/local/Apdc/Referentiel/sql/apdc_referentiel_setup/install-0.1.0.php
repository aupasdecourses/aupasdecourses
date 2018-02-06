<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_referentiel/backupmodif'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true,'nullable'  => false,'primary'=>true,'auto_increment'=>true), 'Entity Id')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Update Time')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true,'nullable'  => false), 'Product Id')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(), 'SKU')
    ->addColumn('reference_interne_magasin', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Référence Interne Magasin')
    ->addColumn('code_ref_apdc', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Code Référentiel APDC')
    ->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Name')
    ->addColumn('description',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Description')
    ->addColumn('price',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(),'Price')
    ->addColumn('prix_public',Varien_Db_Ddl_Table::TYPE_VARCHAR,64,array(),'Prix Public')
    ->addColumn('prix_kilo_site', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Prix Kilo Site')
    ->addColumn('unite_prix', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(),'Prix Kilo Site')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true), 'Tax Class Id')
    ->addColumn('marge_arriere',Varien_Db_Ddl_Table::TYPE_VARCHAR,64,array(),'Marge Arrière')
    ->addColumn('short_description',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Short Description')
    ->addColumn('description',Varien_Db_Ddl_Table::TYPE_TEXT,null,array(),'Description')
    ->addColumn('status',Varien_Db_Ddl_Table::TYPE_SMALLINT,5,array('unsigned'=>true),'Status')
    ->addColumn('weight',Varien_Db_Ddl_Table::TYPE_DECIMAL,'12,4',array(),'Weight')
    ->addColumn('image',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Image')
    ->addColumn('availability_days', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(),'Availability Days')
    ->addColumn('can_order_days', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(),'Availability Days')
    ->addColumn('commercant', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Commercant')
    ->addColumn('labels_produits', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Labels Produits')
    ->addColumn('origine', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Origine')
    ->addColumn('producteur', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Producteur')
    ->addColumn('produit_biologique', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Produit Biologique')
    ->addColumn('produit_de_saison', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(),'Produit De Saison')
    ->addColumn('produit_fragile', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(),'Produit Fragile')
    ->addColumn('suggestion_utilisation', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Suggestion Utilisation')
    ->addColumn('poids_portion',Varien_Db_Ddl_Table::TYPE_VARCHAR,64,array(),'Poids Portion')
    ->addColumn('nbre_portion',Varien_Db_Ddl_Table::TYPE_SMALLINT,10,array(),'Nombre Portion')
    ->addColumn('on_selection', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Nom Cat Commerçant')
    ->addColumn('risque_rupture', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Nom Cat Commerçant')
    ->addColumn('notes_com', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Nom Cat Commerçant')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Nom Cat Commerçant');    
$installer->getConnection()->createTable($table);
$installer->endSetup();
