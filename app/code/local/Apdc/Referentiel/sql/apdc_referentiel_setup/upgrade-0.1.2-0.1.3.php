<?php

/**
 * @category  Apdc
 * @package   Apdc_Referentiel
 */

$installer = $this;
$installer->startSetup();

//ajouter index sur la table référentiel

$tableName = $installer->getTable('apdc_referentiel/referentiel');
// Check if the table already exists
if ($installer->getConnection()->isTableExists($tableName)) {
    $installer->getConnection()->addIndex(
        $installer->getTable('apdc_referentiel/referentiel'),
        $installer->getIdxName('apdc_referentiel/referentiel', array('code_ref_apdc')),
        array('code_ref_apdc')
    );
}
$installer->endSetup();

