<?php

/**	Creation de la table geocode_customer 
 *	Colonnes lattitude et longitude
 *	Jointure avec la table sales_flat_order_address
 *	sales_flat_order_address.street = geocode_customer.address
 **/

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->newTable($installer->getTable('pmainguet_delivery/geocode_customers'))
	->addColumn('geocode_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'auto_increment'	=> true,
		'identity'			=> true,
		'unsigned'			=> true,
		'nullable'			=> false,
		'primary'			=> true,
		'comment'			=> 'Customer_Id',
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
$installer->endSetup();
