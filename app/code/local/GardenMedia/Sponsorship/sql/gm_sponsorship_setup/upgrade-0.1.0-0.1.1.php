<?php

$content = '<h3>Offrez 10€ à un ami pour qu\'il découvre Au Pas de Courses et recevez 10€ lorsqu\'il commande</h3>';

$block = Mage::getModel('cms/block');
$block->setTitle('Sponsorship dashboard');
$block->setIdentifier('gm_sponsorship_dashboard');
$block->setStores(array(0));
$block->setIsActive(1);
$block->setContent($content);
$block->save();

$config = Mage::getModel('core/config_data');
$config->setScope('default');
$config->setScopeId(0);
$config->setPath('gm_sponsorship/general/block_dashboard');
$config->setValue($block->getIdentifier());
$config->save();

