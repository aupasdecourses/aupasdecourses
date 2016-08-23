<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Coupon extends Mage_Core_Helper_Abstract {
 
	
	public function getCouponCodes() {
		$coupons = array();
		//$rules = Mage::getResourceModel('salesrule/rule_collection')->load();;
		$rules = Mage::getResourceModel('salesrule/coupon_collection')->load();
		foreach ($rules as $rule) {
			$code = $rule->getCode();
			if(!empty($code)) {
			//if ($rule->getIsActive()) {
				$coupons[] = (object)$rule->getData(); 

			//}
			}
		}

		return $coupons;
	}
	
	
	public function generateCoupon($coupon_template,$coupon_code=null,$expiration=null) {
	#http://www.magentocommerce.com/boards/viewthread/321216/
	
		// check coupon
		$coupon = Mage::getModel('salesrule/coupon')->load($coupon_template);
		$coupon_template = $coupon->getData('coupon_id');
		if(empty($coupon_template)) return;
		
	
		//check rule
		$rule = Mage::getModel('salesrule/rule')->load($coupon->getData('rule_id'));
		$tester = $rule->getData('rule_id');
		if(empty($tester)) return;
		
	
		// coupon code
		if(!empty($coupon_code)) {
			$tester = Mage::getModel('salesrule/coupon')->load($coupon_code,'code')->getData();
			if(!empty($tester)) $coupon_code = null;
		}
		if(empty($coupon_code)) $coupon_code = $rule->getCouponCodeGenerator()->generateCode();

		
		// expiration
		if(!is_null($expiration)) {
			$expiration = (int)$expiration;
			if($expiration>0) {
				$expiration = date('Y-m-d',time()+(86400*$expiration));
			}
			else $expiration = null;
		}
	
		
		$newCoupon = Mage::getModel('salesrule/rule');//create new rule
		$newCoupon->setData($rule->getData());//copy the data from old rule to new rule
		$newCoupon->unsRuleId();//unset the rule id so it's considered new
		$newCoupon->setFromDate(date('Y-m-d'));//change from date
		$newCoupon->setToDate($expiration);//change to date
		$newCoupon->setName($coupon_code);//change the rule name
		$newCoupon->setCouponCode($coupon_code); //change the rule coupon
		$newCoupon->save();//save new rule
		
		$newcode = Mage::getModel('salesrule/coupon')->load(trim($newCoupon->getCouponCode()),'code')->getData();
		if(empty($newcode['coupon_id'])) return;

		$obj = new stdClass;
		$obj->coupon_id =  $newcode['coupon_id'];
		$obj->coupon_code = $newcode['code'];
		$obj->expiration = $newcode['expiration_date'];
		$obj->rule = $newCoupon;
		
		return $obj;
		
	}

	public function deleteCoupon($coupon_id) {
	
		//load coupon
		$coupon = Mage::getModel('salesrule/coupon')->load($coupon_id);
		$tester = $coupon->getData('coupon_id');
		if(empty($tester)) return;
			
		//delete rule
		$rule = Mage::getModel('salesrule/rule')->load($coupon->getData('rule_id'));
		$rule->delete();
		
		//delete coupon
		$coupon->delete();
		
		return;
	}
	
	
}

