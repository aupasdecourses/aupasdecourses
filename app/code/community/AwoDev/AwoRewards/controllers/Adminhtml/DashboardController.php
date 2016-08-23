<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action {

	/**
	 * Initialize action
	 *
	 * Here, we set the breadcrumbs and the active menu
	 *
	 * @return Mage_Adminhtml_Controller_Action
	 */
	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('promo/awodev_aworewards_coupon')
			->_title($this->__('Promotions'))->_title($this->__('Coupon'))
			->_addBreadcrumb($this->__('Promotions'), $this->__('Promotions'))
			->_addBreadcrumb($this->__('Coupon'), $this->__('Coupon'));
		
		return $this;
	}

	public function indexAction() {
//printrx($this->getFullActionName());
		$this->_initAction()
			->_initLayoutMessages('adminhtml/session')
			->renderLayout();
	}

	public function blockcodeAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('promo/awodev_aworewards/awodev_aworewards_dashboard');
	}
}
