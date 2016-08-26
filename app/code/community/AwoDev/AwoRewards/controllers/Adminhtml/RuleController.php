<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Adminhtml_RuleController extends Mage_Adminhtml_Controller_Action {

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
			->_setActiveMenu('promo/awodev_aworewards_rule')
			->_title($this->__('Promotions'))->_title($this->__('Rule'))
			->_addBreadcrumb($this->__('Promotions'), $this->__('Promotions'))
			->_addBreadcrumb($this->__('Rule'), $this->__('Rule'));
		
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
		$this->_initAction();
		
		// Get id if available
		$id  = $this->getRequest()->getParam('id');
		$model = Mage::getModel('awodev_aworewards/rule');
		
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
		
		$this->_title($model->getId() ? $model->getName() : $this->__('New Rule'));
		
		//$data = Mage::getSingleton('adminhtml/session')->getRuleData(true);
        $data = $this->_getSession()->getData('awodev_aworewards_rule_form_data', true);
		if (!empty($data['rule'])) {
			$model->setData($data['rule']);
		}
		Mage::register('awodev_aworewards_rule_form_data', $model);
		Mage::register('awodev_aworewards_rule_form_messages', $this->getLayout()->getMessagesBlock()->getGroupedHtml());
			
			
			
			
		

		$this->_initAction();
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		}

		$this->_addBreadcrumb(
			$id ? $this->__('Edit Rule') : $this->__('New Rule'),
			$id ? $this->__('Edit Rule') : $this->__('New Rule')
        );

		$this->_initLayoutMessages('adminhtml/session');
		$this->renderLayout();

	}
	
	public function saveAction() {
		if ($postData = $this->getRequest()->getPost()) {
			$model = Mage::getSingleton('awodev_aworewards/rule');
			$model->setData($postData);
			$model->fixforsave();
			$postdata = $model->getData();
	
	
			$errors = $model->validate();
			if(!empty($errors)) {
				$this->_getSession()->setData('awodev_aworewards_rule_form_data',$postdata);
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
				$this->_getSession()->setData('awodev_aworewards_rule_form_data',$postdata);
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
			//printrx($e);
				$this->_getSession()->setData('awodev_aworewards_rule_form_data',$postdata);
				Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving'));
			}

			Mage::getSingleton('adminhtml/session')->setRuleData($postData);
			$this->_redirectReferer();
		}
	}
	
	public function deleteAction() {
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				$model = Mage::getModel('awodev_aworewards/rule');				 
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
		$data = Mage::getModel('awodev_aworewards/rule')->load($this->getRequest()->getParam('id'));
		echo $data->getContent();
	}
	
	
	/**
	 * Check currently called action by permissions for current user
	 *
	 * @return bool
	 */
	protected function _isAllowed() {
		return Mage::getSingleton('admin/session')->isAllowed('promo/awodev_aworewards/awodev_aworewards_rule');
	}
}
