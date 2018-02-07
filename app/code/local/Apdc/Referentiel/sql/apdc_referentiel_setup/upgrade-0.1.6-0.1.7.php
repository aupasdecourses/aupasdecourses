<?php

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie
$tablename=$installer->getTable('apdc_referentiel/referentiel');
$connection = $installer->getConnection();
	$connection->dropColumn($tablename,'type1');
	$connection->dropColumn($tablename,'type2');
	$connection->dropColumn($tablename,'type3');
$installer->endSetup();
