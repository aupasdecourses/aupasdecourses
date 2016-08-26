<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Credit_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Init class
	 */
	public function __construct() {  
		parent::__construct();
		
		$this->setId('awodev_aworewards_credit_edit');
		$this->setTitle($this->__('Credit Information'));
	}  
	
	/**
	 * Setup form fields for inserts/updates
	 *
	 * return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm() {  
		$model = Mage::registry('awodev_aworewards/credit');

		$form = new Varien_Data_Form(array(
			'id'        => 'edit_form',
			'name'        => 'edit_form',
			'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'    => 'post'
		));
		
		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend'    => $this->__('Credit Information'),
			'class'     => 'fieldset-wide',
		));
		
		$fieldset->addField('customer', 'text', array(
			'name'      => 'customer',
			'label'     => $this->__('Customer'),
			'required'  => true,
		));
		$fieldset->addField('user_id', 'hidden', array(
			'name'      => 'user_id',
			'required'  => true,
		));
		
		$fieldset->addField('points', 'text', array(
			'name'      => 'points',
			'label'     => $this->__('Points'),
			'required'  => true,
		));
		
		$fieldset->addField('note', 'textarea', array(
			'name'		=> 'note',
			'label'		=> $this->__('Notes'),
        ));



		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);
		
		return parent::_prepareForm();
	}  
}
