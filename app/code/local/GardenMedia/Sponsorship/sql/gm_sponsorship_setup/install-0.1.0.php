<?php

/**
 * @category  GardenMedia
 * @package   GardenMedia_Sponsorship
 * @copyright Copyright (c) 2016 Garden Media Studio VN
 */

$this->startSetup();


# ADD sponsor table
$table = $this->getConnection()
    ->newTable($this->getTable('gm_sponsorship/sponsor'))
    ->addColumn(
        'sponsor_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
            'primary' => true,
        ),
        'Customer Id'
    )
    ->addColumn(
        'sponsor_code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR,
        255,
        array(
            'nullable' => false,
        ),
        'Sponsor Code'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Sponsor Since'
    )
    ->addIndex(
        $this->getIdxName(
            'gm_sponsorship/sponsor',
            array('sponsor_code')
        ),
        array('sponsor_code')
    )
    ->addForeignKey(
        $this->getFkName(
            'gm_sponsorship/sponsor',
            'sponsor_id',
            'customer/entity',
            'entity_id'
        ),
        'sponsor_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$this->getConnection()->createTable($table);


# ADD godchild table
$table = $this->getConnection()
    ->newTable($this->getTable('gm_sponsorship/godchild'))
    ->addColumn(
        'godchild_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true,
            'primary' => true,
        ),
        'Godchild Id'
    )
    ->addColumn(
        'sponsor_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true
        ),
        'Sponsor Id'
    )
    ->addColumn(
        'created_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Sponsor Since'
    )
    ->addIndex(
        $this->getIdxName(
            'gm_sponsorship/godchild',
            array('sponsor_id')
        ),
        array('sponsor_id')
    )
    ->addForeignKey(
        $this->getFkName(
            'gm_sponsorship/godchild',
            'godchild_id',
            'customer/entity',
            'entity_id'
        ),
        'godchild_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'gm_sponsorship/godchild',
            'sponsor_id',
            'gm_sponsorship/sponsor',
            'sponsor_id'
        ),
        'sponsor_id',
        $this->getTable('gm_sponsorship/sponsor'),
        'sponsor_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$this->getConnection()->createTable($table);



# ALTER salesrule_coupon table
$this->getConnection()->addColumn(
    $this->getTable('salesrule/coupon'),
    'customer_unique',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'comment' => 'Sponsorship unique customer',
        'nullable' => false
    )
);



# ADD New table salesrule_coupon_customer

$table = $this->getConnection()
    ->newTable($this->getTable('gm_sponsorship/salesrule_coupon_customer'))
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ),
        'Coupon customer ID'
    )
    ->addColumn(
        'customer_owner_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true
        ),
        'Customer Owner Id'
    )
    ->addColumn(
        'coupon_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true
        ),
        'Coupon Id'
    )
    ->addColumn(
        'customer_linked_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        11,
        array(
            'nullable' => false,
            'unsigned' => true
        ),
        'Customer Linked Id'
    )
    ->addColumn(
        'owner_type',
        Varien_Db_Ddl_Table::TYPE_TEXT,
        30,
        array(
            'nullable'  => false,
        ),
        'Owner (sponsor or godchild)'
    )
    ->addIndex(
        $this->getIdxName(
            'gm_sponsorship/salesrule_coupon_customer',
            array('customer_owner_id')
        ),
        array('customer_owner_id')
    )
    ->addIndex(
        $this->getIdxName(
            'gm_sponsorship/salesrule_coupon_customer',
            array('customer_linked_id')
        ),
        array('customer_linked_id')
    )
    ->addIndex(
        $this->getIdxName(
            'gm_sponsorship/salesrule_coupon_customer',
            array('coupon_id')
        ),
        array('coupon_id')
    )
    ->addIndex(
        $this->getIdxName(
            'gm_sponsorship/salesrule_coupon_customer',
            array('customer_owner_id', 'coupon_id', 'customer_linked_id')
        ),
        array('customer_owner_id', 'coupon_id', 'customer_linked_id'),
        array(
            'type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        )
    )
    ->addForeignKey(
        $this->getFkName(
            'gm_sponsorship/salesrule_coupon_customer',
            'customer_owner_id',
            'customer/entity',
            'entity_id'
        ),
        'customer_owner_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'gm_sponsorship/salesrule_coupon_customer',
            'coupon_id',
            'salesrule/coupon',
            'coupon_id'
        ),
        'coupon_id',
        $this->getTable('salesrule/coupon'),
        'coupon_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->addForeignKey(
        $this->getFkName(
            'gm_sponsorship/salesrule_coupon_customer',
            'customer_linked_id',
            'customer/entity',
            'entity_id'
        ),
        'customer_linked_id',
        $this->getTable('customer/entity'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$this->getConnection()->createTable($table);



$this->endSetup();
