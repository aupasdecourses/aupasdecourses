<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_RegisterController extends Mage_Core_Controller_Front_Action {

	public function indexAction() {
	
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
			return;
		}
		
		$registration_link = Mage::getURL('customer/account/create');
		
		$id = $this->getRequest()->getParam('id');
		if(empty($id)) {
			$this->_redirectUrl($registration_link);
			return;
		}
		
		$id = (double) Mage::helper('awodev_aworewards/data')->simple_number_decrypt($id);
		if(empty($id)) {
			$this->_redirectUrl($registration_link);
			return;
		}
		
		$id = (int) Mage::getModel("customer/customer")->load($id)->getId();
		if(empty($id)) {
			$this->_redirectUrl($registration_link);
			return;
		}

		$expire_days  = (int) Mage::getStoreConfig('awodev_aworewards/invitation/referral_cookie_expire');
		if(empty($expire_days)) $expire_days = 7;
		if($expire_days>0) {
			setcookie('aworewards_referral_id', $id, time()+(86400*$expire_days));
		}
		
		Mage::getSingleton('customer/session')->setData('aworewards_referral_id',$id);

		$this->_redirectUrl($registration_link);
		return;
	}
	
	
	
}

