<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Payment extends Mage_Core_Block_Template {

	public function __construct() {
		parent::__construct();

        $payments = Mage::getResourceModel('awodev_aworewards/payment_collection')
            ->addFieldToFilter('main_table.user_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->setOrder('main_table.payment_date', 'desc')
        ;

        $this->setPayments($payments);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('awodev_aworewards')->__('My Payments'));
    }

	public function _prepareLayout(){  
        parent::_prepareLayout();
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'aworewards.payment.pager'); 
		$pager->setCollection($this->getPayments());   
		$this->setChild('pager', $pager);
		$this->getPayments()->load();
		
		return $this;                
    }
	
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}


}