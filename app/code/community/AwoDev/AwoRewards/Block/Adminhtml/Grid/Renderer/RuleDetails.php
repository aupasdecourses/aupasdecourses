<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Adminhtml_Grid_Renderer_RuleDetails extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
	
		$helper = Mage::helper('awodev_aworewards');

		$details = '';
		$points = $row->getData('points');
		$params = $row->getData('params');
		$coupon_id = (int)$row->getData('template_id');
		
		

		if(!empty($params)) $params = Mage::helper('core')->jsonDecode($params);
		
		if(!empty($points)) $details .= '<div>'.$helper->__('Points').': '.number_format($points,2).'</div>';
		if(!empty($params->order_percent)) $details .= '<div>'.$helper->__('Points').': '.$params->order_percent.'% '.$helper->__('Order Total').'</div>';
		if(!empty($coupon_id)) {
			$coupon = Mage::getModel('salesrule/coupon')->load($coupon_id);
			//$coupondata = $coupon->getData();
			//printrx($coupondata);
			$details .= '<div>'.$helper->__('Coupon to Copy').': '.$coupon->getData('code').'</div>';
		}
		if(!empty($params->coupon_expiration)) $details .= '<div>'.$helper->__('Coupon Expiration').': '.$params->coupon_expiration.'</div>';

		if($row->getData('rule_type')=='order') {
			if(!empty($params->order_min_type)) $details .= '<div>'.$helper->__('Minimum Order Total Type').': '.$helper->vars('order_min_type',$params->order_min_type).'</div>';
			if(!empty($params->order_min)) $details .= '<div>'.$helper->__('Minimum Order Total').': '.number_format($params->order_min,2).'</div>';
			if(!empty($params->order_trigger)) $details .= '<div>'.$helper->__('Order Number to trigger rule').': '.$params->order_trigger.'</div>';
		}

		return $details;
    }
} 