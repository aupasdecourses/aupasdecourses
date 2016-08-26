<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Referral_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Init class
	 */
	public function __construct() {  
		$this->_blockGroup = 'awodev_aworewards';
		$this->_controller = 'adminhtml_referral';
		
		parent::__construct();
		
		$this->_updateButton('save', 'label', $this->__('Save Referral'));
		$this->_updateButton('delete', 'label', $this->__('Delete Referral'));
	}  
	
	/**
	 * Get Header text
	 *
	 * @return string
	 */
	public function getHeaderText() {  
		if (Mage::registry('awodev_aworewards/referral')->getId()) {
			return $this->__('Edit Referral');
		}  
		else {
			return $this->__('New Referral');
		}  
	}  
}
