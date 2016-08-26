<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_License extends AwoDev_AwoRewards_Block_Adminhtml_Widget_Grid_Container {
	public function __construct() {
        $this->_blockGroup = 'awodev_aworewards';
        $this->_controller = 'adminhtml_license';
        $this->_headerText = $this->__('License');
         
        parent::__construct();
    }
	
	
	public function getLicense() {
		return Mage::getModel('awodev_aworewards/license')->getLiData();		
	}
}