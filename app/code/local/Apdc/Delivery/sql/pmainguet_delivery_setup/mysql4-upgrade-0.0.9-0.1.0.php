<?php

/** Ajout de la colonne former_address
 *	dans la table geocode_customers
 *	ainsi deux colonnes adresses : une pour la jointure (former)
 *	et une colonne adresse propre pour encodage latitude longitude
 */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode'),'former_address', array(
		'type'		=>	Varien_Db_Ddl_Table::TYPE_TEXT,
		'nullable'	=> true,
		'default'	=> null,
		'comment'	=> 'Ancienne adresse pour jointure sales_order',
	));

$installer->endSetup();
