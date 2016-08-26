<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Adminhtml_System_Config_Source_Paypalfeetype {
	protected $_options;

	public function toOptionArray() {
		return array(
			array('value' => 'EACHRECEIVER', 'label'=>Mage::helper('awodev_aworewards')->__('Each Receiver')),
			array('value' => 'SENDER', 'label'=>Mage::helper('awodev_aworewards')->__('Sender')),
		);
	}
}
