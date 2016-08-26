<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_CreditController extends Mage_Core_Controller_Front_Action {

	public function preDispatch() {
		parent::preDispatch();

		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
		}
	}

	public function indexAction() {
//Mage::helper('awodev_aworewards/payment')->paypal('seyipaypal2@mailinator.com');
//echo Mage::helper('core')->currency(24.99200, true, false);exit;
		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}

	public function payPaypalAction() {
		$_r = Mage::helper('awodev_aworewards/payment')->paypal(trim($this->getRequest()->getPost('paypal_email')));

		if(!empty($_r)) {
			if(!empty($_r['error'])) {
				Mage::getSingleton('customer/session')->addError($_r['error']);
				$this->_redirect('*/*/');
				return;
			}
			elseif(!empty($_r['success'])) {
				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Payment successfully made'));
				$this->_redirect('*/payment');
				return;
			}
		}
		$this->_redirect('*/*/');
		return;
	}

	function payCouponAction() {
		$_r = Mage::helper('awodev_aworewards/payment')->coupon('request');
		if(!empty($_r)) {
			if(!empty($_r['error'])) {
				Mage::getSingleton('customer/session')->addError($_r['error']);
				$this->_redirect('*/*/');
				return;
			}
			elseif(!empty($_r['success'])) {
				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Coupon successfully generated'));
				$this->_redirect('*/payment');
				return;
			}
		}
		$this->_redirect('*/*/');
		return;
		if(!class_exists('AwoRewardsRewardsHandler')) require _PS_MODULE_DIR_.'aworewards/lib/rewardshandler.php';
		$_r = AwoRewardsRewardsHandler::triggerPayment($this->context->customer->id,'request');
		if(!empty($_r['errorcode'])) Tools::redirect('index.php?fc=module&module=aworewards&controller=credit&error='.$_r['errorcode']);
		else Tools::redirect('index.php?fc=module&module=aworewards&controller=payment&success=4');
	}
}

