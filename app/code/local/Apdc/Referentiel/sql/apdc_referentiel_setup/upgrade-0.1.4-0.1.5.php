<?php

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->addColumn(
        $installer->getTable('apdc_referentiel/referentiel'),
        'name_inter',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'length'    => 255,
            'comment'   => 'name_inter',
            'after'     => 'code_inter',
        )
    );

$installer->endSetup();
