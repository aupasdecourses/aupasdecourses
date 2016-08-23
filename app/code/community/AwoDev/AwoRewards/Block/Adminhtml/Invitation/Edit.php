<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Invitation_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Init class
	 */
	public function __construct() {  

		$this->_blockGroup = 'awodev_aworewards';
		$this->_controller = 'adminhtml_invitation';

		parent::__construct();

	}  
	
	/**
	 * Get Header text
	 *
	 * @return string
	 */
	public function getHeaderText() {  
		return $this->__('Invitation');
	} 

	protected function _prepareLayout() {
		// Load Wysiwyg on demand and Prepare layout
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled() && ($block = $this->getLayout()->getBlock('head'))) {
			$block->setCanLoadTinyMce(true);
		}
		parent::_prepareLayout();
	} 	
}
