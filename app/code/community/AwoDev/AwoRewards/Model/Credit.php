<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Credit extends Mage_Core_Model_Abstract {
	protected function _construct() {  
		$this->_init('awodev_aworewards/credit');
	}

 	public function fixforsave() {
	
		$data = $this->getData();

		// set null fields
		if(empty($data['note'])) $data['note'] = null;
		if(!empty($data['points'])) $data['points'] = (int)$data['points'];
		if(empty($data['credit_type'])) $data['credit_type'] = 'points';
		if(empty($data['entry_type'])) $data['entry_type'] = 'admin';
		if(empty($data['timestamp'])) $data['timestamp'] = date('Y-m-d h:i:s');

		$this->setData($data);
	}

	public function validate() {
        $errors = array();
        $helper = Mage::helper('awodev_aworewards');
		
		$data = (object) $this->getData();
		
		
		if(empty($data->user_id) || !ctype_digit($data->user_id)) $errors[] = $helper->__('Customer').': '.$helper->__('please enter a valid value');
		else {
			$customer_id = (int) Mage::getModel("customer/customer")->load($data->user_id)->getId();
			if(empty($customer_id)) $errors[] = $helper->__('Customer').': '.$helper->__('please enter a valid value');
		}
		if (empty($data->points) || $data->points<=0) $errors[] = $helper->__('Points').': '.$helper->__('please enter a valid value');
		
		
		return $errors;
	}
	
	public function save() {

		$id = (int)$this->getdata('id');
		$_isnew = empty($id) ? true : false;

		parent::save();

		//// trigger auto points if new entry
		if($_isnew) {
			$user_id = (int)$this->getData('user_id');
			if(!empty($user_id)) Mage::helper('awodev_aworewards/payment')->coupon('automatic',$user_id);
		}
		
	}

    public function load($id, $field=null) {
		parent::load($id,$field);
		
		$affobj = Mage::getModel("customer/customer")->load($this->getData('user_id'))->getData();
		if(!empty($affobj->entity_id)) {
			$customer = $affobj['lastname'].', '.$affobj['firstname'].' '.$affobj['email'];
			if(!Mage::app()->isSingleStoreMode()) $customer = '['.Mage::app()->getWebsite($affobj['website_id'])->getName().'] '.$customer;
			$this->setData('customer',$customer);
		}
		
		return $this;
	}

    public function delete() {
		
		$item = Mage::getModel('awodev_aworewards/credit')->load($this->getId())->getData();
		
		if(!empty($item['payment_id'])) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awodev_aworewards')->__('Please delete payment attached to credit id %d',$item['id']));
			return false;
		}
		
		if(!empty($item['coupon_id'])) Mage::helper('awodev_aworewards/coupon')->deleteCoupon($item['coupon_id']);
		
		parent::delete();
		return true;
	}

}
