<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		
		// Set some defaults for our grid
		$this->setDefaultSort('id');
		$this->setId('awodev_aworewards_rule_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getCollectionClass() {
		// This is the model we are using for the grid
		return 'awodev_aworewards/rule_collection';
	}
	
	protected function _prepareCollection() {
		// Get and set our collection for the grid
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns() {
		// Add the columns that should appear in the grid
		$this->addColumn('id',array(
			'header'=> Mage::helper('awodev_aworewards')->__('ID'),
			'align' =>'right',
			'width' => '50px',
			'index' => 'id',
		));
		
		$this->addColumn('rule_name',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Name'),
				'index' => 'rule_name',
			)
		);
		
		$this->addColumn('rule_type', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Rule Type'),
            //'align'     => 'left',
            //'width'     => '100px',
            'index'     => 'rule_type',
            'type'      => 'aworewardsvar',
            'escape'    => true,
            //'sortable'  => false,
            //'filter'    => false,
            'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
 			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('rule_type'),
       )); 		
		$this->addColumn('customer_type', array(
			'header'=> Mage::helper('awodev_aworewards')->__('Customer Type'),
			'index' => 'customer_type',
			'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('customer_type'),
		));
		
		$this->addColumn('credit_type', array(
			'header'=> Mage::helper('awodev_aworewards')->__('Credit Type'),
			'index' => 'credit_type',
			'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('credit_type'),
		));
		
		$this->addColumn('startdate',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Start Date'),
			'type'      => 'datetime',
            'align'     => 'center',
			'index' => 'startdate',
			'gmtoffset' => true
		));

		$this->addColumn('expiration',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Expiration'),
			'type'      => 'datetime',
            'align'     => 'center',
			'index' => 'expiration',
			'gmtoffset' => true
		));

		$this->addColumn('note',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Note'),
			'index' => 'note'
		));
		$this->addColumn('details',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Details'),
			'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_RuleDetails,
            'sortable'  => false,
            'filter'  => false,
			'width'=>'30%',
		));
		$this->addColumn('ordering',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Ordering'),
			'align'     => 'right',
			'index' => 'ordering',
            'filter'  => false,
		));
		$this->addColumn('published',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Status'),
			'index'=> 'published',
			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('published'),
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
                        'caption'   => Mage::helper('customer')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
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
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
