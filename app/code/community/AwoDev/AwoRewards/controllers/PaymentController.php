<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class Awodev_AwoRewards_PaymentController extends Mage_Core_Controller_Front_Action {

	public function preDispatch() {
		parent::preDispatch();

		if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->setFlag('', 'no-dispatch', true);
			$this->_redirectUrl(Mage::helper('customer')->getAccountUrl());
		}
	}

	public function indexAction() {

		$this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}


}

