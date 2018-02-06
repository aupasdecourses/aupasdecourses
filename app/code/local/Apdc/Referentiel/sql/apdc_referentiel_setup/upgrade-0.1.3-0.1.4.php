<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->addColumn(
        $installer->getTable('apdc_referentiel/referentiel'),
        'code_inter',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'comment'   => 'code_inter',
            'after'     => 'code_taxonomie',
        )
    );

$installer->endSetup();
