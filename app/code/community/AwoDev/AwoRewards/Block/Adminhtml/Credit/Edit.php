<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Credit_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Init class
	 */
	public function __construct() {  
		$this->_blockGroup = 'awodev_aworewards';
		$this->_controller = 'adminhtml_credit';
		
		parent::__construct();
		
		$this->_updateButton('save', 'label', $this->__('Save Credit'));
		$this->_updateButton('delete', 'label', $this->__('Delete Credit'));
	}  
	
	/**
	 * Get Header text
	 *
	 * @return string
	 */
	public function getHeaderText() {  
		if (Mage::registry('awodev_aworewards/credit')->getId()) {
			return $this->__('Edit Credit');
		}  
		else {
			return $this->__('New Credit');
		}  
	}  
}
