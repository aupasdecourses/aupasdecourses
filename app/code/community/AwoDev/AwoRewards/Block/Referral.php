<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Referral extends Mage_Core_Block_Template {

	public function __construct() {
		parent::__construct();

        $referrals = Mage::getResourceModel('awodev_aworewards/referral_collection')
            ->addFieldToFilter('user_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
           ->setOrder('email', 'asc')
            ->setOrder('send_date', 'desc')
        ;
		$referrals->getSelect()->joinLeft(
				array('u2' => Mage::helper('awodev_aworewards')->getTable('customer_entity')),
				'u2.entity_id=main_table.join_user_id',
				array('u2.email AS friend_email')
			);
		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
		$referrals->getSelect()
				->joinLeft(	array('cf1' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')),
						'cf1.entity_id=u2.entity_id AND cf1.attribute_id='.$fn->getAttributeId(), 
						 array('firstname' => 'value')
				)
				->joinLeft(	array('cf2' => Mage::helper('awodev_aworewards')->getTable('customer_entity_varchar')), 
						'cf2.entity_id=u2.entity_id AND cf2.attribute_id='.$ln->getAttributeId(), 
						array('lastname' => 'value')
				)
				->columns(new Zend_Db_Expr("CONCAT(`cf1`.`value`, ' ',`cf2`.`value`) AS friend_name"));

        $this->setReferrals($referrals);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('awodev_aworewards')->__('My Referrals'));
    }

	public function _prepareLayout(){  
        parent::_prepareLayout();
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'aworewards.referral.pager'); 
//$pager->setAvailableLimit(array(3=>3,10=>10,20=>20,'all'=>'all'));  
		$pager->setCollection($this->getReferrals());   
		$this->setChild('pager', $pager);
		$this->getReferrals()->load();
		
		return $this;                
    }
	
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}


}