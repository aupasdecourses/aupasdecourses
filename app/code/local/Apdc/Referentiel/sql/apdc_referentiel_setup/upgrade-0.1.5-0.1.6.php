<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_referentiel/produit_base'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true,'nullable'  => false,'primary'=>true,'auto_increment'=>true), 'Entity Id')
    ->addColumn('type',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Type')
    ->addColumn('categorie',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Categorie')
    ->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Name')
    ->addColumn('code_inter', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(),'Code Référentiel APDC');
$installer->getConnection()->createTable($table);
$installer->endSetup();
