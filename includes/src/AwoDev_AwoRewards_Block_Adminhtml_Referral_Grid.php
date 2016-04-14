<?php

class Awodev_AwoRewards_Block_Adminhtml_Referral_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	public function __construct() {
		parent::__construct();
		
		// Set some defaults for our grid
		$this->setDefaultSort('id');
		$this->setId('awodev_aworewards_referral_grid');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _getCollectionClass() {
		// This is the model we are using for the grid
		return 'awodev_aworewards/referral_collection';
	}
	
	protected function _prepareCollection() {
		// Get and set our collection for the grid
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		$this->setCollection($collection);
		
		$collection->getSelect()
                ->join(
					array('u' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
					"u.entity_id = main_table.user_id",
					array('u.email AS affiliate_email','u.website_id')
				)
                ->joinLeft(
					array('u2' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
					'u2.entity_id=main_table.join_user_id',
					array('u2.email AS friend_email')
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
				->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS affiliate_name"));
	
	
		$collection->getSelect()
				->joinLeft(	array('cf1' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')),
						'cf1.entity_id=u2.entity_id AND cf1.attribute_id='.$fn->getAttributeId(), 
						 array('firstname' => 'value')
				)
				->joinLeft(	array('cf2' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')), 
						'cf2.entity_id=u2.entity_id AND cf2.attribute_id='.$ln->getAttributeId(), 
						array('lastname' => 'value')
				)
				->columns(new Zend_Db_Expr("CONCAT(`cf1`.`value`, ' ',`cf2`.`value`) AS friend_name"));
	
	
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
		
		$this->addColumn('affiliate_name',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Affiliate'),
				'index' => 'affiliate_name'
			)
		);
		
		$this->addColumn('email',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Email'),
				'index' => 'email'
			)
		);
		
		$this->addColumn('send_date',array(
			'header'=> Mage::helper('awodev_aworewards')->__('First Send Date'),
			'type'      => 'date',
            'align'     => 'center',
			'index' => 'send_date',
			'gmtoffset' => true
		));

		$this->addColumn('last_sent_date',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Last Sent Date'),
			'type'      => 'date',
            'align'     => 'center',
			'index' => 'last_sent_date',
			'gmtoffset' => true
		));

		$this->addColumn('friend_name',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Friend'),
				'index' => 'friend_name'
			)
		);
		
		$this->addColumn('join_date',array(
			'header'=> Mage::helper('awodev_aworewards')->__('Join Date'),
			'type'      => 'date',
            'align'     => 'center',
			'index' => 'join_date',
			'gmtoffset' => true
		));

 		$this->addColumn('customer_msg',
			array(
				'header'=> Mage::helper('awodev_aworewards')->__('Affiliate Message'),
				'index' => 'customer_msg',
				'renderer'  => new AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_Textarea,
			)
		);
		
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
