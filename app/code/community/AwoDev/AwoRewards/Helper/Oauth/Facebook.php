<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Oauth_Facebook extends AwoDev_AwoRewards_Helper_Oauth {

	# https://developers.facebook.com/
	# https://developers.facebook.com/docs/reference/api/publishing/
	
	public $requestTokenURL = '';
	public $authorizeURL = 'https://www.facebook.com/dialog/oauth';
	public $accessTokenURL = 'https://graph.facebook.com/oauth/access_token';
	public $graphsURL = 'https://graph.facebook.com';

	public $contactsURL = 'https://graph.facebook.com/me/friends';
	
	public $debug = 0;

	function init($callback='') {
		$is_enabled = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_facebook_enabled');;
		if(!empty($is_enabled)) {
			$this->oauth_consumer_key = Mage::getStoreConfig('awodev_aworewards/external_api/social_facebook_key');
			$this->oauth_consumer_secret = Mage::getStoreConfig('awodev_aworewards/external_api/social_facebook_secret');
			$this->callback = $callback;
		}
	}

	function get_request_token($usePost=false, $passOAuthInHeader=false) {
		$this->http_code = 200;
		return array(
			'oauth_token'=>'not needed',
			'oauth_token_secret'=>'not needed',
		);
	}
	
	function get_login_url($token, $sign_in_with_twitter = TRUE, $getter_type=null) {
		//return $this->authorizeURL.'?client_id='.$this->oauth_consumer_key.'&redirect_uri='.$this->callback.'&response_type=code&scope=read_friendlists,user_about_me';
		//return $this->authorizeURL.'?client_id='.$this->oauth_consumer_key.'&redirect_uri='.$this->callback.'&client_secret=$secret&scope=publish_stream,offline_access,read_stream,manage_pages&response_type=token

		$scope = 'publish_stream,publish_actions,user_likes';
		if(!empty($getter_type)) {
			if($getter_type=='postpublic') $scope = 'publish_stream,publish_actions';
			elseif($getter_type=='likeus') $scope = 'user_likes';
		}
		return $this->authorizeURL.'?client_id='.$this->oauth_consumer_key.'&redirect_uri='.$this->rfc3986_encode($this->callback).'&response_type=code&scope='.$scope;
	}
	
	function get_access_token($request_token, $request_token_secret, $oauth_verifier, $usePost=false, $passOAuthInHeader=true) {
		$this->extra_params = array(
			'client_id'=>$this->oauth_consumer_key,
			'client_secret'=>$this->oauth_consumer_secret,
			'redirect_uri'=>$this->callback,
			'code'=>$request_token,
		);
		$rtn = parent::get_access_token($request_token, $request_token_secret, $oauth_verifier, $usePost, $passOAuthInHeader);
		$this->extra_params = array();
		return $rtn;
	}


    function get_contacts ($access_token, $emails_count, $usePost=false, $passOAuthInHeader=true, $port=443) {
		// scope needed: read_friendlists,user_about_me
		$this->extra_params = array(
			'client_id'=>$this->oauth_consumer_key,
			'access_token'=>$access_token['access_token'],
			'redirect_uri'=>$this->callback,
			//'code'=>$request_token,
		);
		$rtn = parent::get_contacts($access_token, $emails_count, $usePost, $passOAuthInHeader);
		$this->extra_params = array();
		return $rtn;
	}

  
	function postPublic ($access_token, $message) {
		// scope needed: publish_stream
		$params = array(
			'client_id'=>$this->oauth_consumer_key,
			'access_token'=>(!empty($access_token['access_token']) ? $access_token['access_token'] : ''),
			'redirect_uri'=>$this->callback,
			'message'=>$message,
		);
		
		$obj = $this->api($this->graphsURL.'/me/feed','facebook_wall',$params,$access_token,true);
		
		$rtn = array();
		if(!empty($obj['error'])) $rtn['error'] = '('.$obj['error']['code'].') '.$obj['error']['message'];
		elseif(empty($obj['id'])) $rtn['error'] = 'error';
		else $rtn['id'] = $obj['id'];

		return $rtn;

	}

	
	function likeUS($access_token, $facebookpage) {
	# http://stackoverflow.com/questions/6830497/check-if-user-already-likes-fanpage#answer-12774076

		$params = array(
			'client_id'=>$this->oauth_consumer_key,
			'access_token'=>(!empty($access_token['access_token']) ? $access_token['access_token'] : ''),
			'redirect_uri'=>$this->callback,
		);
		
		$obj = array();
		$facebook_obj = $this->api($this->graphsURL.'/'.$facebookpage,'facebook_like',$params,$access_token);
		if(!empty($facebook_obj['id'])) $obj = $this->api($this->graphsURL.'/me/likes/'.$facebook_obj['id'],'facebook_like',$params,$access_token);
		
		
		$rtn = array();
		if(!empty($obj['error']) || empty($obj['data'][0]['id']) || $obj['data'][0]['id']!=$facebook_obj['id']) $rtn['error'] = 'error';
		else $rtn['id'] = 'liked';

		return $rtn;

	}

}
