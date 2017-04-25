<?php

/** Modif de la table geocode :
 *	Ajout d'une colonne 'id_customer' pour jointure avec table customer_entity
 *	La colonne 'geocode_id' sert de PK AI NN */

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode_customer'),'id_customer', array(
		'type'	=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		'nullable'	=> true,
		'default'	=> 0,
		'comment'	=> 'jointure table customer_entity',
	));

$installer->endSetup();
