<?php

/**	Creation de la table geocode 
 *	Colonnes lattitude et longitude
 *	Jointure avec la table sales_flat_order_address
 *	sales_flat_order_address.street = geocode.address
 **/

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->newTable($installer->getTable('pmainguet_delivery/geocode_customers'))
	->addColumn('geocode_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'auto_increment'	=> true,
		'identity'			=> true,
		'unsigned'			=> true,
		'nullable'			=> false,
		'primary'			=> true,
		'comment'			=> 'Geocode_Id',
		))
	->addColumn('address', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'comment'			=> 'Adresse',
		))
	->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'			=> false,
		'default'			=> 0,
		'comment'			=> 'Code Postal',
		))
	->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
		'comment'			=> 'Ville',
		))
	->addColumn('lat', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,9', array(
		'default'			=> 0.0,
		'comment'			=> 'Latitude',
		))
	->addColumn('long', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,9', array(
		'default'			=> 0.0,
		'comment'			=> 'Longitude',
		));

$installer->getConnection()->createTable($table);

/* Ajout de la colonne former_address
 *	dans la table geocode_customers
 *	ainsi deux colonnes adresses : une pour la jointure (former)
 *	et une colonne adresse propre pour encodage latitude longitude
 */

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode_customers'),'former_address', array(
		'type'		=>	Varien_Db_Ddl_Table::TYPE_TEXT,
		'nullable'	=> true,
		'default'	=> null,
		'comment'	=> 'Ancienne adresse pour jointure sales_order',
	));

/** Modif de la table geocode :
 *	Ajout d'une colonne 'id_customer' pour jointure avec table customer_entity
 *	La colonne 'geocode_id' sert de PK AI NN */

$table = $installer->getConnection()
->addColumn($installer->getTable('pmainguet_delivery/geocode_customers'),'id_customer', array(
	'type'	=> Varien_Db_Ddl_Table::TYPE_INTEGER,
	'nullable'	=> true,
	'default'	=> 0,
	'comment'	=> 'jointure table customer_entity',
));

/** Modif de la table geocode :
 *	Ajout de la colonne 'id_shop' 
 *	pour differencier shop de client */

$installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode_customers'), 'id_shop', array(
		'type'	=> Varien_Db_Ddl_Table::TYPE_INTEGER,
		'nullable'	=> true,
		'default'	=> 0,
		'comment'	=> 'same that apdc_shop id',
	));

/** Modif de la table geocode :
 *	Ajout de la colonne 'whoami' 
 *	pour differencier shop de client */

$installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/geocode_customers'), 'whoami', array(
		'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
		'length'	=> 255,
		'nullable'	=> true,
		'default'	=> null,
		'comment'	=> 'customer or shop',
	));

$installer->endSetup();
