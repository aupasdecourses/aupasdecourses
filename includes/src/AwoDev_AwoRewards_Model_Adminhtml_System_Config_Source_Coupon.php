<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Adminhtml_System_Config_Source_Coupon {
	protected $_options;

	public function toOptionArray($isMultiselect) {
        if (!$this->_options) {
			$coupons = Mage::helper('awodev_aworewards/coupon')->getCouponCodes();
			foreach($coupons as $coupon) $this->_options[] = array('value'=>$coupon->coupon_id,'label'=>$coupon->code);
		}
		$options = $this->_options;
		return $options;
	}
}
