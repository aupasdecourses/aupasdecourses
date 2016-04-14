<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Model_Referral extends Mage_Core_Model_Abstract {
	protected function _construct() {  
		$this->_init('awodev_aworewards/referral');
	}

 	public function fixforsave() {
		$data = $this->getData();
		
		// set null fields
		if(empty($data['ip'])) $data['ip'] = null;
		if(empty($data['last_sent_date'])) $data['last_sent_date'] = $data['send_date'];
		if(empty($data['customer_msg'])) $data['customer_msg'] = null;
		$data['join_user_id'] = null;
		$data['join_date'] = null;

		$this->setData($data);
	}

	public function validate() {
        $errors = array();
        $helper = Mage::helper('awodev_aworewards');
		
		$data = (object) $this->getData();
		
		
		if(empty($data->user_id) || !ctype_digit($data->user_id)) $errors[] = $helper->__('Affiliate').': '.$helper->__('please enter a valid value');
		else {
			$affiliate_id = (int) Mage::getModel("customer/customer")->load($data->user_id)->getId();
			if(empty($affiliate_id)) $errors[] = $helper->__('Affiliate').': '.$helper->__('please enter a valid value');
		}
		if (empty($data->email) || !Zend_Validate::is($data->email, 'EmailAddress')) $errors[] = $helper->__('Friend\'s email').': '.$helper->__('please enter a valid value');
		if(!preg_match("/^\d{4}\-\d{2}\-\d{2}$/",$data->send_date)) $errors[] = $helper->__('First Send Date').': '.$helper->__('please enter a valid value');
		if(!empty($data->last_sent_date) && !preg_match("/^\d{4}\-\d{2}\-\d{2}$/",$data->last_sent_date)) $errors[] = $helper->__('Last Sent Date').': '.$helper->__('please enter a valid value');
		
		
		return $errors;
	}
	
	public function save() {
		$data = $this->getData();
	
	
		if(!isset($data['id'])) $data['id'] = 0;
		$data['id'] = (int)$data['id'];
		$_isnew = empty($data['id']) ? true : false;
	
	
		$friend = Mage::getModel("customer/customer")
					->setWebsiteId(Mage::getModel("customer/customer")->load($data['user_id'])->getWebsiteId())
					->loadByEmail($data['email'])
					->getData();
		//echo '<pre>'; print_r($friend);exit;
		if(!empty($friend)) {
			$this->setData('join_user_id',$friend['entity_id']);
			$this->setData('join_date',$friend['created_at']);
		}

		parent::save();

	}

    public function load($id, $field=null) {
		parent::load($id,$field);
		
		$affobj = Mage::getModel("customer/customer")->load($this->getData('user_id'))->getData();
		$affiliate = $affobj['lastname'].', '.$affobj['firstname'].' '.$affobj['email'];
		if(!Mage::app()->isSingleStoreMode()) $affiliate = '['.Mage::app()->getWebsite($affobj['website_id'])->getName().'] '.$affiliate;
		$this->setData('affiliate',$affiliate);
		
		return $this;
	}


}
