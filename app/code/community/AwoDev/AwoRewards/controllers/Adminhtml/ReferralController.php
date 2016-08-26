<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_ReferralController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('promo/awodev_aworewards_referral')
			->_title($this->__('Promotions'))->_title($this->__('Referral'))
			->_addBreadcrumb($this->__('Promotions'), $this->__('Promotions'))
			->_addBreadcrumb($this->__('Referral'), $this->__('Referral'));
		
		return $this;
	}

	public function indexAction() {  
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		$this->_initAction()
			->renderLayout();
	}  

	public function newAction() {  
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		$this->_forward('edit');
    }  
     
	public function editAction() {  
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		// Get id if available
		$id  = $this->getRequest()->getParam('id');
		$model = Mage::getModel('awodev_aworewards/referral');
		
		if ($id) {
			// Load record
			$model->load($id);
			
			// Check if record is loaded
			if (!$model->getId()) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('This item no longer exists'));
				$this->_redirect('*/*/');
				
				return;
			}  
		}  
		
		$this->_title($model->getId() ? $model->getName() : $this->__('New Referral'));
		
		$data = $this->_getSession()->getData('awodev_aworewards_referral_form_data');
		if(!empty($data)) {
			$model->setData($data);
			$this->_getSession()->unsetData('awodev_aworewards_referral_form_data'); 
		}
		else {
			$data = Mage::getSingleton('adminhtml/session')->getReferralData(true);
			if (!empty($data)) {
				$model->setData($data);
			}  
		}
		
		//$this->_initLayoutMessages('adminhtml/session');
		//$msg = Mage::getSingleton('adminhtml/session')->getMessages(); 
		//	->_addContent($this->getLayout()->getMessagesBlock()->addMessages($msg))
		Mage::register('awodev_aworewards/referral', $model);
		$this->_initAction()
			->_addBreadcrumb($id ? $this->__('Edit Referral') : $this->__('New Referral'), $id ? $this->__('Edit Referral') : $this->__('New Referral'))
			->_addContent($this->getLayout()->createBlock('awodev_aworewards/adminhtml_referral_edit')->setData('action', $this->getUrl('*/*/save')))
			->_initLayoutMessages('adminhtml/session')
			->renderLayout();
	}
	
	public function saveAction() {
		if ($postData = $this->getRequest()->getPost()) {
			$model = Mage::getSingleton('awodev_aworewards/referral');
			$model->setData($postData);
			$model->fixforsave();
			$postdata = $model->getData();
	
			$errors = $model->validate();
			if(!empty($errors)) {
				$this->_getSession()->setData('awodev_aworewards_referral_form_data',$postdata);
				foreach ($errors as $er) Mage::getSingleton('adminhtml/session')->addError($er);
				$this->_redirect('*/*/edit');
				return;
			}

			try {
				$model->save();

				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The item has been saved'));
				$this->_redirect('*/*/');

				return;
			}  
			catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving'));
			}

			Mage::getSingleton('adminhtml/session')->setReferralData($postData);
			$this->_redirectReferer();
		}
	}
	
	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('awodev_aworewards/referral');				 
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

	public function messageAction() {
		$data = Mage::getModel('awodev_aworewards/referral')->load($this->getRequest()->getParam('id'));
		echo $data->getContent();
	}
	
	
	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('promo/awodev_aworewards/awodev_aworewards_referral');
	}
}
