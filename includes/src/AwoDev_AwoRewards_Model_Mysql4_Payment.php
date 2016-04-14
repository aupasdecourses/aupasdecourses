<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Mysql4_Payment extends Mage_Core_Model_Mysql4_Abstract {
	protected function _construct() {  
		$this->_init('awodev_aworewards/payment', 'id');
	}  
}