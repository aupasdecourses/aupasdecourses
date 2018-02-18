<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie

$table = $installer->getConnection()
    ->newTable($installer->getTable('apdc_referentiel/categoriesbase'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true,'nullable'  => false,'primary'=>true,'auto_increment'=>true), 'Entity Id')
    ->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Name')
    ->addColumn('url',Varien_Db_Ddl_Table::TYPE_TEXT,NULL,array(),'Url');
$installer->getConnection()->createTable($table);
$installer->endSetup();
