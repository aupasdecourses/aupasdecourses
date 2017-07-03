<?php

/**	Ajout de la colonne "diffprixcommercant' pour la différence entre total commande & total commercant
 *  dans Refund Input sur Indi
 *  Cette 'diffprixcommercant' sera réutilisé dans la facturation pour les avoirs
 *
 **/

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
	->addColumn($installer->getTable('pmainguet_delivery/refund_items'),'diffprixcommercant', array(
		'type'		=> Varien_Db_Ddl_Table::TYPE_FLOAT,
		'nullable'	=> true,
		'default'	=> 0,
		'comment'	=> 'Différence prix commercant',
	));

$installer->endSetup();
