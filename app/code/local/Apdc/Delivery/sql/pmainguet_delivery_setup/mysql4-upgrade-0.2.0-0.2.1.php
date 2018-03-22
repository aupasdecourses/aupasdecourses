<?php

$this->startSetup();

$this->getConnection()->changeColumn(
	$this->getTable('amorderattach/order_field'),
	'commentaires_ticket',
	'commentaires_commercant',
	['type' => Varien_Db_Ddl_Table::TYPE_TEXT]
);

$this->endSetup();