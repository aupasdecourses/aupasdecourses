<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Mysql4_Referral_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	protected function _construct() {  
		$this->_init('awodev_aworewards/referral');
	}  
}