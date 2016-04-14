<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Oauth_Yahoo extends AwoDev_AwoRewards_Helper_Oauth {

	# http://developer.yahoo.com/

	public $requestTokenURL = 'https://api.login.yahoo.com/oauth/v2/get_request_token';
	public $authorizeURL = '';
	public $accessTokenURL = 'https://api.login.yahoo.com/oauth/v2/get_token';
	public $contactsURL = 'https://social.yahooapis.com/v1/user/%guid%/contacts';
	
	public $debug = 0;

	public $guid;
	
	function init($callback='') {
		$is_enabled = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_yahoo_enabled');;
		if(!empty($is_enabled)) {
			$this->oauth_consumer_key = Mage::getStoreConfig('awodev_aworewards/external_api/social_yahoo_key');
			$this->oauth_consumer_secret = Mage::getStoreConfig('awodev_aworewards/external_api/social_yahoo_secret');
			$this->callback = $callback;
		}
	}
	

	function get_login_url($token) {
		if(!empty($token['xoauth_request_auth_url'])) return urldecode($token['xoauth_request_auth_url']);
		return '';
	}

	function get_access_token($request_token, $request_token_secret, $oauth_verifier, $usePost=false, $passOAuthInHeader=true) {
		$data = parent::get_access_token($request_token, $request_token_secret, $oauth_verifier, $usePost, $passOAuthInHeader);
		if(!empty($data['xoauth_yahoo_guid'])) $this->guid = $data['xoauth_yahoo_guid'];
		return $data;
	}
	function override_contact_url() { return str_replace('%guid%',$this->rfc3986_decode($this->guid),$this->contactsURL); }
	
	function get_contacts ($access_token, $emails_count, $usePost=false, $passOAuthInHeader=false, $port=443) {
		$data = parent::get_contacts ($access_token, $emails_count, $usePost, $passOAuthInHeader, $port);
		$rtn = array();
		if(!empty($data['contacts']['contact'])) {
			foreach($data['contacts']['contact'] as $item) {
				$name = $email = '';
				foreach($item['fields'] as $field) {
					if($field['type']=='email') $email = strtolower($field['value']);
					elseif($field['type']=='name') $name = $field['value']['givenName'].' '.$field['value']['familyName'];
				}
				if(!empty($email)) $rtn[$email] = array('name'=>$name,'email'=>$email);
			}
		}
		return $rtn;
	}

  
}
