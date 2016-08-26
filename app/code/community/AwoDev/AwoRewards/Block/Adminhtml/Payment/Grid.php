<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_Block_Adminhtml_Payment_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		
		// Set some defaults for our grid
		$this->setDefaultSort('id');
		$this->setId('awodev_aworewards_payment_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getCollectionClass() {
		// This is the model we are using for the grid
		return 'awodev_aworewards/payment_collection';
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
		
		$this->addColumn('payment_type', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Payment Type'),
            'index'     => 'main_table.payment_type',
            'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
 			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('payment_type'),
       )); 		
		$this->addColumn('amount_paid', array(
			'header'=> Mage::helper('awodev_aworewards')->__('Amount Paid'),
			'index' => 'main_table.amount_paid',
			'type' => 'price',
		));
		
		$this->addColumn('payment_date',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Payment Date'),
			'type'      => 'datetime',
            'align'     => 'center',
			'index' => 'payment_date',
			'gmtoffset' => true
		));
		
		$this->addColumn('payment_details',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Payment Details'),
			'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_PaymentDetail,
            'sortable'  => false,
            'filter'  => false,
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
//		{autoOpen: false,modal: true,resizable: true,width: "auto",height: "auto"}
		//$("#dialog").load("yourajaxhandleraddress.htm").dialog({dialogoptions});
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
