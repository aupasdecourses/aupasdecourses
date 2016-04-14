<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Block_Invitation extends Mage_Core_Block_Template {

	
	public function canInvite() {
		//permissions
		$cando = true;
		$check_order = (int) Mage::getStoreConfig('awodev_aworewards/invitation/order_required');
		if($check_order == 1) {
			$cando = Mage::helper('awodev_aworewards')->isCustomerOrdered();
		}
		return $cando;
	}
	public function is_google_import() {
		$session = Mage::getSingleton('customer/session');
		$is_import = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_google_enabled');
		if($is_import) {
			$data = $session->getData('aworewards_email_list_google');
			if(!empty($data)) $is_import = false;
		}
		
		return $is_import;
	}
	public function is_yahoo_import() {
		$session = Mage::getSingleton('customer/session');
		$is_import = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_yahoo_enabled');
		if($is_import) {
			$data = $session->getData('aworewards_email_list_yahoo');
			if(!empty($data)) $is_import = false;
		}
		
		return $is_import;
	}
	
	public function is_facebook() {
		return Mage::getStoreConfig('awodev_aworewards/external_api/social_facebook_enabled');
	}
	
	public function is_twitter() {
		return Mage::getStoreConfig('awodev_aworewards/external_api/social_twitter_enabled');
	}
	
	public function getInvitationDescription() {
		return Mage::getStoreConfig('awodev_aworewards/invitation/frontend_description');
	}
	
	public function getEmailLImit() {
		$limit = (int) Mage::getStoreConfig('awodev_aworewards/invitation/maxemailperinvite');
		if(empty($limit)) $limit = 30;
		return $limit;
	}
	public function getLoadedEmails () {
		$session = Mage::getSingleton('customer/session');
		
		$contacts = array();
		$allowed = array('google','yahoo','upload');
		foreach($allowed as $getter) {
			$data = $session->getData('aworewards_email_list_'.$getter);
			if(!empty($data)) $contacts[$getter] = $data;
		}
		
		$customer_id = Mage::getSingleton('customer/session')->getCustomerId();		
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$select = $read->select()
			->from($resource->getTableName('awodev_aworewards/referral'), 'email')
			->where('user_id=?', $customer_id);
		$used_emails = $read->fetchCol($select);

		$emails_display = array();
		if(!empty($contacts)) {
			$burned_emails = array();
			foreach($contacts as $getter=>$emails) {
				if(empty($emails)) continue;
				foreach($emails as $email=>$r) {
					if(!empty($burned_emails[$email])) {
						unset($contacts[$getter][$email]);
						continue;
					}
					if(in_array($email,$used_emails)) {
						unset($contacts[$getter][$email]);
						continue;
					}
					
					$emails_display[$getter][] = $email;
					$burned_emails[$email] = 1;
				}
			}
		}
		
		// update session
		foreach($allowed as $getter) {
			if(isset($contacts[$getter]))
				$session->setData('aworewards_email_list_'.$getter,$contacts[$getter]);
		}
		
		return $emails_display;
	}
	public function getInvitations() {
		
		$customer = Mage::getModel("customer/customer")->load(Mage::getSingleton('customer/session')->getCustomerId())->getData();
		$customer_link = Mage::helper('awodev_aworewards/data')->registration_link();
		$customer_link = '<a href="'.$customer_link.'">'.$customer_link.'</a>';


		$customer_name = $customer['firstname'].' '.$customer['lastname'];
		$collection = Mage::getModel('awodev_aworewards/invitation')
					->getCollection()
					->addFieldToFilter('published',1)
					->addFieldToFilter('website_id',Mage::app()->getWebsite()->getId())
					->setOrder('ordering', 'ASC')
		;
		foreach($collection as $item) {
			$row = (object)$item->getDataFront();
			$dd_desc = $row->description;
			if(!empty($dd_desc)) {
				$message = $row->email_body;
				if(!empty($message)) {
					$body	= str_replace(array('{user_name}','{customer_link}'),array($customer_name,$customer_link),$message);
					$body1 = $body2 = ''; $body_isnote = false;
					$pos = strpos($body,'{customer_note}');
					if( $pos === false) $body1 = $body;
					else {
						$body_isnote = true;
						$body1 = substr($body,0,$pos);
						$body2 = substr($body,$pos+15);
					}
					$body = strip_tags(Mage::helper('awodev_aworewards')->br2nl($body));

					$invitations[$row->invitation_type][$row->id] = array('dd'=>$dd_desc, 'isnote'=>$body_isnote, 'body'=>$body, 'body1'=>$body1, 'body2'=>$body2);
				}
			}
		}
		
		return $invitations;

	}


}