<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Referral_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	/**
	 * Init class
	 */
	public function __construct() {  
		parent::__construct();
		
		$this->setId('awodev_aworewards_referral_edit');
		$this->setTitle($this->__('Referral Information'));
	}  
	
	/**
	 * Setup form fields for inserts/updates
	 *
	 * return Mage_Adminhtml_Block_Widget_Form
	 */
	protected function _prepareForm() {  
		$model = Mage::registry('awodev_aworewards/referral');
		//echo '<pre>'; print_r($model);exit;
		$form = new Varien_Data_Form(array(
			'id'        => 'edit_form',
			'name'        => 'edit_form',
			'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method'    => 'post'
		));
		
		$fieldset = $form->addFieldset('base_fieldset', array(
			'legend'    => $this->__('Referral Information'),
			'class'     => 'fieldset-wide',
		));
		
		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id',
			));
		}  
		
		$fieldset->addField('affiliate', 'text', array(
			'name'      => 'affiliate',
			'label'     => $this->__('Affiliate'),
			'required'  => true,
		));
		$fieldset->addField('user_id', 'hidden', array(
			'name'      => 'user_id',
			'required'  => true,
		));
		
		$fieldset->addField('email', 'text', array(
			'name'      => 'email',
			'label'     => $this->__('Friend\'s email'),
			'required'  => true,
		));
		
		$fieldset->addField('send_date', 'date', array( 
			'name'		=> 'send_date',
			'label'     => $this->__('First Send Date'),
			'input_format' => 'yyyy-MM-dd',
			'format'	=> 'yyyy-MM-dd',
			'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
			'required'	=> true,
			//'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ));
		
		$fieldset->addField('last_sent_date', 'date', array( 
			'name'		=> 'last_sent_date',
			'label'     => $this->__('Last Sent Date'),
			'input_format' => 'yyyy-MM-dd',
			'format'	=> 'yyyy-MM-dd',
			'image'  => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
        ));

		
		$fieldset->addField('customer_msg', 'textarea', array(
			'name'		=> 'customer_msg',
			'label'		=> $this->__('Affiliate Message'),
        ));


		$fieldset->addField('ip', 'text', array(
			'name'      => 'ip',
			'label'     => $this->__('IP Address'),
		));


		$form->setValues($model->getData());
		$form->setUseContainer(true);
		$this->setForm($form);
		
		return parent::_prepareForm();
	}  
}
