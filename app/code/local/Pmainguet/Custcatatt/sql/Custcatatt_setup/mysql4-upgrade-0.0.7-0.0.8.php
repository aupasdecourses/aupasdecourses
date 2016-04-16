<?php
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_category','is_clickable');

$installer->endSetup();