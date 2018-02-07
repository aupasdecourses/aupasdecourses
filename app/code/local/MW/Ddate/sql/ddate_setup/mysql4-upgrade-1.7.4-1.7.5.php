<?php

$installer = $this;
$installer->startSetup();

//ajouter champs permettant d'identifier la personne qui modifie
$tablename=$installer->getTable('ddate/dtime');
$connection = $installer->getConnection();
	$connection->modifyColumn($tablename,'special_day','TEXT');
$installer->endSetup();
