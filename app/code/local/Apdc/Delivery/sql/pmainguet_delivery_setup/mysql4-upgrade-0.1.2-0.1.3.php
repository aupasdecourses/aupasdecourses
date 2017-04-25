<?php

/** Modif de la table geocode :
 *	Ajout de la colonne 'whoami' 
 *	pour differencier shop de client */

$installer = $this;
$installer->startSetup();

$installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode_customers'), 'whoami', array(
		'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
		'length'	=> 255,
		'nullable'	=> true,
		'default'	=> null,
		'comment'	=> 'customer or shop',
	));

$installer->endSetup();
