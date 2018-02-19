<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie
$tableName = $installer->getTable('apdc_referentiel/categoriesposition');
$table = $installer->getConnection()
    ->newTable($tableName)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array('unsigned'  => true,'nullable'  => false,'primary'=>true,'auto_increment'=>true), 'Entity Id')
    ->addColumn('name',Varien_Db_Ddl_Table::TYPE_VARCHAR,255,array(),'Name')
    ->addColumn('parent',Varien_Db_Ddl_Table::TYPE_TEXT,NULL,array(),'Parent')
    ->addColumn('g_parent',Varien_Db_Ddl_Table::TYPE_TEXT,NULL,array(),'Great Parent')
    ->addColumn('g_g_parent',Varien_Db_Ddl_Table::TYPE_TEXT,NULL,array(),'Great great Parent')
    ->addColumn('position',Varien_Db_Ddl_Table::TYPE_INTEGER,11,array(),'Position');
$installer->getConnection()->createTable($table);


// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName)) {
    $installer->getConnection()->addIndex(
        $installer->getTable('apdc_referentiel/categoriesposition'),
        $installer->getIdxName('apdc_referentiel/categoriesposition', array('name')),
        array('name')
    );
}

$installer->endSetup();
