<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter index sur la table référentiel

$tableName = $installer->getTable('apdc_referentiel/categoriesbase');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName)) {
    $installer->getConnection()->addIndex(
        $installer->getTable('apdc_referentiel/categoriesbase'),
        $installer->getIdxName('apdc_referentiel/categoriesbase', array('name')),
        array('name')
    );
}
$installer->endSetup();
