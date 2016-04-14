<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Observer {

	public function triggerRegister($observer) {
	// customer_register_success
		try {
			Mage::helper('awodev_aworewards/reward_registration')->processRegistration($observer->getCustomer()->getData());
		}
		catch(Exception $e) {
			Mage::log('AwoDev_AwoRewards_Model_Observer::triggerRegister::unsuccessful::'.$e->getMessage());		
		}
	}
	
	public function triggerOrder($observer) {
	// sales_order_payment_pay
		try {
			$order = $observer->getInvoice()->getOrder(); // Mage_Sales_Model_Order
			Mage::helper('awodev_aworewards/reward_order')->processOrder($order->getData());
		}
		catch(Exception $e) {
			Mage::log('AwoDev_AwoRewards_Model_Observer::triggerOrder::unsuccessful::'.$e->getMessage());		
		}

	}
	
	public function cron() {
	// system cron
		Mage::log("AwoDev_AwoRewards_Model_Observer::cron processing");		
 		try {
			Mage::helper('awodev_aworewards/reward_registration')->processRegistrationSys();
		}
		catch(Exception $e) {
			Mage::log('AwoDev_AwoRewards_Model_Observer::cron::registration::unsuccessful::'.$e->getMessage());		
		}
 		try {
			Mage::helper('awodev_aworewards/reward_review')->processReviewSys();
		}
		catch(Exception $e) {
			Mage::log('AwoDev_AwoRewards_Model_Observer::cron::review::unsuccessful::'.$e->getMessage());		
		}
	}
	
	public function triggerCustomerLogout() {
		$session = Mage::getSingleton('customer/session');
		$session->unsetData('aworewards_oauth_token');
		$session->unsetData('aworewards_oauth_token_secret');
		$session->unsetData('aworewards_email_list_upload');
		$session->unsetData('aworewards_email_list_google');
		$session->unsetData('aworewards_email_list_yahoo');
	}
}
