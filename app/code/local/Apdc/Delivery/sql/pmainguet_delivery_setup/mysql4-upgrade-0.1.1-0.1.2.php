<?php

/** Modif de la table geocode :
 *	Ajout de la colonne 'id_shop' 
 *	pour differencier shop de client */

$installer = $this;
$installer->startSetup();

$installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode_customers'), 'id_shop', array(
		'type'	=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		'nullable'	=> true,
		'default'	=> 0,
		'comment'	=> 'same that apdc_shop id',
	));

$installer->endSetup();
