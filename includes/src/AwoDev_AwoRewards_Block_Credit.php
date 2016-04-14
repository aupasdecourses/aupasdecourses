<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Credit extends Mage_Core_Block_Template {

	public function __construct() {
		parent::__construct();

        $credits = Mage::getResourceModel('awodev_aworewards/credit_collection')
            ->addFieldToFilter('main_table.user_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
			->setOrder('main_table.timestamp', 'desc')
        ;
		
		$credits->getSelect()->joinLeft(
				array('p' => Mage::helper('awodev_aworewards')->getTable('awodev_aworewards_payment')),
				'p.id=main_table.payment_id',
				array('p.payment_details')
			);

        $this->setCredits($credits);

        Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('awodev_aworewards')->__('My Credits'));
    }

	public function _prepareLayout(){  
        parent::_prepareLayout();
		
		$pager = $this->getLayout()->createBlock('page/html_pager', 'aworewards.credit.pager'); 
		$pager->setCollection($this->getCredits());   
		$this->setChild('pager', $pager);
		$this->getCredits()->load();
		
		return $this;                
    }
	
	public function getPagerHtml() {
		return $this->getChildHtml('pager');
	}

	public function getPointTotal() {
		$points = Mage::helper('awodev_aworewards')->getCustomerPointTotal();
		$this->setPoints($points);
		return $points;
	}
	
	public function is_payout_coupon() {
		
		$is_payout = false;
		if(Mage::getStoreConfig('awodev_aworewards/payout_coupon/enabled')) {
			$points = $this->getPoints();
			$coupon_template = Mage::getStoreConfig('awodev_aworewards/payout_coupon/coupon_template');
			$email_subject = Mage::getStoreConfig('awodev_aworewards/payout_coupon/email_subject');
			$email_template = Mage::getStoreConfig('awodev_aworewards/payout_coupon/email_template');
			$minimum = (float)Mage::getStoreConfig('awodev_aworewards/payout_coupon/minimum');
			$point_ratio = (float)Mage::getStoreConfig('awodev_aworewards/payout_coupon/point_ratio');
			if(empty($point_ratio)) $point_ratio = 1;
			$points['unclaimed'] = round($points['unclaimed'],2);

			if(!empty($points['unclaimed'])
			&& !empty($coupon_template)
			&& !empty($email_subject)
			&& !empty($email_template)
			&& $minimum<=($points['unclaimed']/$point_ratio)
			) $is_payout = true;
		}
		return $is_payout;
	}
	
	public function is_payout_paypal() {
	
	
		$is_payout = false;
		if(Mage::getStoreConfig('awodev_aworewards/payout_paypal/enabled')) {
			$points = $this->getPoints();
			$minimum = Mage::getStoreConfig('awodev_aworewards/payout_paypal/minimum');
			$point_ratio = (float)Mage::getStoreConfig('awodev_aworewards/payout_paypal/point_ratio');
			if(empty($point_ratio)) $point_ratio = 1;
			$points['unclaimed'] = round($points['unclaimed'],2);
			
			if(!empty($points['unclaimed'])
			&& $minimum<=($points['unclaimed']/$point_ratio)
			) $is_payout = true;
		}
		
		return $is_payout;
	}
	
	public function getPaypalWarning() {
		return Mage::getStoreConfig('awodev_aworewards/payout_paypal/warning');
	}

}