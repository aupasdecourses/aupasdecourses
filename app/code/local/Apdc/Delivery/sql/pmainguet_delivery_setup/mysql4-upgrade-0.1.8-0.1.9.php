<?php

$this->startSetup();

$this->getConnection()->dropColumn($this->getTable('amorderattach/order_field'), 'commentaires_commande');
$this->getConnection()->dropColumn($this->getTable('amorderattach/order_field'), 'screenshot');

$this->endSetup();