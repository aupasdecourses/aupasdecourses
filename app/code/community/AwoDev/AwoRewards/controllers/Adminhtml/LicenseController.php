<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_LicenseController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('promo/awodev_aworewards_license')
			->_title($this->__('Promotions'))->_title($this->__('License'))
			->_addBreadcrumb($this->__('Promotions'), $this->__('Promotions'))
			->_addBreadcrumb($this->__('License'), $this->__('License'));
		
		return $this;
	}

	public function indexAction() { $this->_forward('edit'); }  
	public function newAction() { $this->_forward('edit'); }  
     
	public function editAction() {  
		$this->_initAction();
		
		// Get id if available
		$model = Mage::getModel('awodev_aworewards/license');
		
		$this->_title($model->getId() ? $model->getName() : $this->__('License'));

		$this->_addBreadcrumb($this->__('License'), $this->__('License'));

		$this->_initLayoutMessages('adminhtml/session');
		$this->renderLayout();

	}
	
	public function updateexpiredAction() {
		$model = Mage::getSingleton('awodev_aworewards/license');

		if ( $model->check() ) {
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('License Activated'));
			$this->_redirect('*/adminhtml_dashboard');
			return;
		} else {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Error Activating License'));
		}
	}
	
	

	public function saveAction() {
		if ($postData = $this->getRequest()->getPost()) {
			switch($postData['task']) {
				case 'activate': {
					$model = Mage::getSingleton('awodev_aworewards/license');
					if ( $model->activate($postData) ) {
						Mage::getSingleton('adminhtml/session')->addSuccess($this->__('License Activated'));
						$this->_redirect('*/adminhtml_dashboard');
						return;
					} else {
						Mage::getSingleton('adminhtml/session')->addError($this->__('Error Activating License'));
					}
					break;
				}
				case 'updatelocalkey': {
					$model = Mage::getSingleton('awodev_aworewards/license');
					if ( $model->update_localkey($postData['license'],$postData['local_key']) ) {
						Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Local key updated'));
						$this->_redirect('*/adminhtml_dashboard');
						return;
					} else {
						Mage::getSingleton('adminhtml/session')->addError($this->__('Error updating local key'));
					}
					break;
				}
			}

		}
			$this->_redirect('*/*');
			return;
	}
	
	public function deleteAction() {
		try {
			Mage::getModel('awodev_aworewards/license')->uninstall();		
			
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Item was successfully deleted'));
			$this->_redirect('*/*/');
		}
		catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
		}

		$this->_redirect('*/*/');
	}

	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('promo/awodev_aworewards/awodev_aworewards_license');
	}
}
