<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Init class
	 */
	public function __construct() {  

		$this->_blockGroup = 'awodev_aworewards';
		$this->_controller = 'adminhtml_rule';

		parent::__construct();

		/*
		//$this->_data['template'] = 1;
		$this->setTemplate('awodev/aworewards/Adminhtml/rule.phtml');
		
		$this->_updateButton('save', 'label', $this->__('Save Rule'));
		$this->_updateButton('delete', 'label', $this->__('Delete Rule'));
		$this->_removeButton('reset');
		//*/

	}  
	
	/**
	 * Get Header text
	 *
	 * @return string
	 */
	public function getHeaderText() {  
		return $this->__('Rule');
	} 

	protected function _prepareLayout() {
		// Load Wysiwyg on demand and Prepare layout
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
			$block->setCanLoadTinyMce(true);
		}
		parent::_prepareLayout();
	} 	
}
