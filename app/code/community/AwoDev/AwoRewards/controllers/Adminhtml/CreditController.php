<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_CreditController extends Mage_Adminhtml_Controller_Action {

	protected function _initAction() {
		$this->loadLayout()
			// Make the active menu match the menu config nodes (without 'children' inbetween)
			->_setActiveMenu('promo/awodev_aworewards_credit')
			->_title($this->__('Promotions'))->_title($this->__('Credit'))
			->_addBreadcrumb($this->__('Promotions'), $this->__('Promotions'))
			->_addBreadcrumb($this->__('Credit'), $this->__('Credit'));
		
		return $this;
	}

	public function indexAction() {  
//Mage::helper('awodev_aworewards/reward_review')->processReviewSys();
//printrx(Mage::getModel('sales/order')->load(34)->getData());
//Mage::helper('awodev_aworewards/reward')->processRegistrationSys();
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		$this->_initAction()
			->renderLayout();
	}  

	public function newAction() {  
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		$model = Mage::getModel('awodev_aworewards/credit');
		
		$this->_title($model->getId() ? $model->getName() : $this->__('New Credit'));
		
		$data = $this->_getSession()->getData('awodev_aworewards_credit_form_data');
		if(!empty($data)) {
			$model->setData($data);
			$this->_getSession()->unsetData('awodev_aworewards_credit_form_data'); 
		}
		else {
			$data = Mage::getSingleton('adminhtml/session')->getCreditData(true);
			if (!empty($data)) {
				$model->setData($data);
			}  
		}
		
		Mage::register('awodev_aworewards/credit', $model);
		$this->_initAction()
			->_addBreadcrumb($this->__('New Credit'), $this->__('New Credit'))
			->_addContent($this->getLayout()->createBlock('awodev_aworewards/adminhtml_credit_edit')->setData('action', $this->getUrl('*/*/save')))
			->_initLayoutMessages('adminhtml/session')
			->renderLayout();
    }  
     
	public function editAction() {  
		$myawo = Mage::getModel('awodev_aworewards/license')->getlocal(); if(!@eval($myawo->evaluation)){$this->_redirect('*/adminhtml_license'); return; }
		return;
	}
	
	public function saveAction() {
		if ($postData = $this->getRequest()->getPost()) {
			$model = Mage::getSingleton('awodev_aworewards/credit');
			$model->setData($postData);
			$model->fixforsave();
			$postdata = $model->getData();
	
			$errors = $model->validate();
			if(!empty($errors)) {
				$this->_getSession()->setData('awodev_aworewards_credit_form_data',$postdata);
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

			Mage::getSingleton('adminhtml/session')->setCreditData($postData);
			$this->_redirectReferer();
		}
	}
	
	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('awodev_aworewards/credit');				 
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
				$collection = Mage::getModel('awodev_aworewards/credit')
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
		return Mage::getSingleton('admin/session')->isAllowed('promo/awodev_aworewards/awodev_aworewards_credit');
	}
}
