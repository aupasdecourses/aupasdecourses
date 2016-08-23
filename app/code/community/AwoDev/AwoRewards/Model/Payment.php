<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Payment extends Mage_Core_Model_Abstract {
	protected function _construct() {  
		$this->_init('awodev_aworewards/payment');
	}

    public function delete() {
		
		$item = Mage::getModel('awodev_aworewards/payment')->load($this->getId())->getData();

		if(!empty($item['coupon_id'])) Mage::helper('awodev_aworewards/coupon')->deleteCoupon($item['coupon_id']);
		
		$collection = Mage::getModel('awodev_aworewards/credit')
				->getCollection()
				->addFieldToFilter('payment_id',$item['id'])
		;
		
		foreach($collection as $item) {
			$item->setData('coupon_id',null);
			$item->setData('payment_id',null);
			$item->setData('points_paid',null);
			$item->save();
		}

		parent::delete();
	}
}
