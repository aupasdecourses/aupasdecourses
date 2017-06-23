<?php

/** Ajout de la colonne 'prix_commercant' dans la table 'pmainguet_delivery/refund_items' 
 *	pour les totaux commercants dans Refund Input sur Indi
 **/

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/refund_items'),'prix_commercant', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_FLOAT,
		'nullable'	=> true,
		'default'	=> NULL,
		'comment'	=> 'Total Commercant',
	));

$installer->endSetup();
