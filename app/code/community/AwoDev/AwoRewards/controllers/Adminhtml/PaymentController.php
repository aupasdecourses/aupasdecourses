<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_PaymentController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('promo/awodev_aworewards_payment')
			->_title($this->__('Promotions'))->_title($this->__('Payment'))
			->_addBreadcrumb($this->__('Promotions'), $this->__('Promotions'))
			->_addBreadcrumb($this->__('Payment'), $this->__('Payment'));
		
		return $this;
	}

	public function indexAction() {  
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		$this->_initAction()
			->renderLayout();
	}  
	
	public function detailsAction() {
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		$this->loadLayout();
		$this->renderLayout();
	}

	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('awodev_aworewards/payment');				 
				$model->setId($this->getRequest()->getParam('id'))->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awodev_aworewards')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction() {
		$ids = $this->getRequest()->getParam('ids');
		if(!is_array($ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awodev_aworewards')->__('Please select item(s)'));
		} 
		else {
			try {
	//	printrx($ids);
				$collection = Mage::getModel('awodev_aworewards/payment')
						->getCollection()
						->addFieldToFilter('id',array('in'=>$ids))
				;
				
				$total = 0;
				foreach($collection as $item) {
					if($item->delete()) $total++;
				}
				
				if(!empty($total)) Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were deleted.', $total));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/');
	}	

	

	
	
	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('promo/awodev_aworewards/awodev_aworewards_payment');
	}
}
