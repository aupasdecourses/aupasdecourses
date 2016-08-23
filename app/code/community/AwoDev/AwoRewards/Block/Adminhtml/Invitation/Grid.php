<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_Block_Adminhtml_Invitation_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		
		// Set some defaults for our grid
		$this->setDefaultSort('id');
		$this->setId('awodev_aworewards_invitation_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getCollectionClass() {
		// This is the model we are using for the grid
		return 'awodev_aworewards/invitation_collection';
	}
	
	protected function _prepareCollection() {
		// Get and set our collection for the grid
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		
		$collection->getSelect()
                ->joinLeft(
					array('sr' => Mage::helper('awodev_aworewards')->getTable('salesrule_coupon')),
					"sr.coupon_id = main_table.coupon_template",
					array('sr.code AS coupon_code')
				)
                ->joinLeft(
					array('l' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_locale')),
					'l.entity="invitation" AND l.entity_id=main_table.id AND l.col="description" AND l.store_id=0',
					array('l.value AS l_description')
				)
			;
                
        //$collection->getSelect()->group('item.quote_id');		
		
		
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
		
		$this->addColumn('invitation_name',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Invitation Name'),
				'index' => 'invitation_name',
			)
		);
		
		$this->addColumn('invitation_type', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Invitation Type'),
            'index'     => 'invitation_type',
            'type'      => 'aworewardsvar',
            'escape'    => true,
            'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Var,
 			'type' => 'options',
			'options' => Mage::helper('awodev_aworewards')->vars('invitation_type'),
		)); 		

		$this->addColumn('description', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Description'),
            'index'     => 'l_description',
            'type'      => 'aworewardsvar',
            'escape'    => true,
		)); 		
		$this->addColumn('coupon_code', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Coupon Code'),
            'index'     => 'coupon_code',
		)); 		
		$this->addColumn('coupon_expiration', array(
            'header'    => Mage::helper('awodev_aworewards')->__('Coupon Expiration'),
            'index'     => 'coupon_expiration',
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
