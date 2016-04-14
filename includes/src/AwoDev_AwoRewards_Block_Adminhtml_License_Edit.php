<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_License_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
	 * Init class
	 */
	public function __construct() {  

		$this->_blockGroup = 'awodev_aworewards';
		$this->_controller = 'adminhtml_license';

		parent::__construct();

	}  
	
	/**
	 * Get Header text
	 *
	 * @return string
	 */
	public function getHeaderText() {  
		return $this->__('License');
	} 
}
