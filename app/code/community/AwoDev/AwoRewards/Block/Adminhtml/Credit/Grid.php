<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_Block_Adminhtml_Credit_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		
		// Set some defaults for our grid
		$this->setDefaultSort('id');
		$this->setId('awodev_aworewards_credit_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getCollectionClass() {
		// This is the model we are using for the grid
		return 'awodev_aworewards/credit_collection';
	}
	
	protected function _prepareCollection() {
		// Get and set our collection for the grid
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		$this->setCollection($collection);
		
		$collection->getSelect()
                ->join(
					array('u' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
					"u.entity_id = main_table.user_id",
					array('u.email','u.website_id')
				)
                ->joinLeft(
					array('r' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_rule')),
					"r.id = main_table.rule_id",
					array('r.rule_name')
				)
                ->joinLeft(
					array('o' => Mage::helper('awodev_aworewards')->getTable('sales_flat_order')),
					'o.entity_id = main_table.item_id AND main_table.rule_type="order"',
					array('o.increment_id AS order_number')
				)
                ->joinLeft(
					array('c' => Mage::helper('awodev_aworewards')->getTable('salesrule_coupon')),
					'c.coupon_id = main_table.coupon_id',
					array('c.code AS coupon_code')
				)
			;
		
			
		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
		$collection->getSelect()
				->join(	array('ce1' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')),
						'ce1.entity_id=u.entity_id', 
						 array('firstname' => 'value')
				)
				->where('ce1.attribute_id='.$fn->getAttributeId()) 
				->join(	array('ce2' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')), 
						'ce2.entity_id=u.entity_id', 
						array('lastname' => 'value')
				)
				->where('ce2.attribute_id='.$ln->getAttributeId()) 
				->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));
	
	

		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns() {
		// Add the columns that should appear in the grid
		$this->addColumn('id',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('ID'),
				'align' =>'right',
				'width' => '50px',
				'index' => 'id'
			)
		);
		
		$this->addColumn('customer_name',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Customer'),
				'index' => 'customer_name'
			)
		);
		
		$this->addColumn('email',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Email'),
				'index' => 'email'
			)
		);
		$this->addColumn('rule_name',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Rule'),
				'index' => 'rule_name'
			)
		);
		$this->addColumn('rule_type', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Rule Type'),
            'index'     => 'main_table.rule_type',
            'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
 			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('rule_type'),
       )); 		
		$this->addColumn('credit_type', array(
			'header'=> Mage::helper('awodev_aworewards')->__('Credit Type'),
			'index' => 'main_table.credit_type',
			'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('credit_type'),
		));
		$this->addColumn('order_number',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Order Number'),
				'index' => 'order_number'
			)
		);
		$this->addColumn('coupon_code',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Coupon Code'),
				'index' => 'coupon_code'
			)
		);
		$this->addColumn('points',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Points'),
				'index' => 'points'
			)
		);
		$this->addColumn('points_paid',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Paid'),
				'index' => 'points_paid'
			)
		);
		$this->addColumn('timestamp',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Timestamp'),
			'type'      => 'datetime',
            'align'     => 'center',
			'index' => 'timestamp',
			'gmtoffset' => true
		));
       if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('customer')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index'     => 'website_id',
            ));
        }
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('customer')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
					//array(
					//	'caption'   => Mage::helper('awodev_aworewards')->__('Details'),
					//	'url'       => array('base'=> '*/*/details'),
					//	'field'     => 'id',
					//	'popup'   => true,
					//	'onclick'	=> '',
					//),
                    array(
                        'caption'   => Mage::helper('awodev_aworewards')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
						'confirm' => Mage::helper('awodev_aworewards')->__('Are you sure?'),
                    ),
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		return parent::_prepareColumns();
	}
     
	public function getRowUrl($row) {
		// This is where our row data will link to
		//return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
	protected function _prepareMassaction() {
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('ids');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('awodev_aworewards')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
			'confirm' => Mage::helper('awodev_aworewards')->__('Are you sure?')
		));
		return $this;
	}

}
