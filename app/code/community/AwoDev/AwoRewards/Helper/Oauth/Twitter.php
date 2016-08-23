<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Oauth_Twitter extends AwoDev_AwoRewards_Helper_Oauth {
	
	# https://dev.twitter.com/apps

	public $requestTokenURL = 'https://api.twitter.com/oauth/request_token';
	public $authorizeURL = 'https://twitter.com/oauth/authorize';
	public $accessTokenURL = 'https://api.twitter.com/oauth/access_token';
	public $contactsURL = 'https://api.twitter.com/1.1/friends/list.json';
	public $followusURL = 'https://api.twitter.com/1.1/friendships/create.json';
	
	
		
	public $tweetURL = 'https://api.twitter.com/1.1/statuses/update.json';

	
	public $debug = 0;

	function init($callback='') {
		$is_enabled = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_twitter_enabled');;
		if(!empty($is_enabled)) {
			$this->oauth_consumer_key = Mage::getStoreConfig('awodev_aworewards/external_api/social_twitter_key');
			$this->oauth_consumer_secret = Mage::getStoreConfig('awodev_aworewards/external_api/social_twitter_secret');
			$this->callback = $callback;
		}
	}
	

	function get_login_url($token, $sign_in_with_twitter = TRUE) {
		if (is_array($token)) {
			$token = $token['oauth_token'];
		}
		return $this->authorizeURL.'?oauth_token='.$token;
	}
  
  
	function postPublic($access_token, $message) {
		$params = array('status'=>$message,'oauth_token'=>(!empty($access_token['oauth_token']) ? $access_token['oauth_token'] : ''),);
		$response = $this->oAuthRequest($this->tweetURL, 'postTweet', $params,!empty($access_token['oauth_token_secret']) ? $access_token['oauth_token_secret']:null, true);

		$json_object = array();
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			if ($body) {
				$this->logit("postTweet:INFO:response:");
				$json_object = json_decode($this->json_pretty_print($body), true);
			}
		}

		$rtn = array();
		if(!empty($json_object['errors'])) {
			$rtn['error'] = '';
			foreach($json_object['errors'] as $t) $rtn['error'] .= '<div>'.$t['message'].'</div>';
			if(empty($rtn['error'])) $rtn['error'] = 'error';
		}
		elseif(empty($json_object['id_str'])) $rtn['error'] = 'error';
		else $rtn['id'] = $json_object['id_str'];

		return $rtn;


		
	}
	
	function likeUS($access_token,$user) {
		$params = array('oauth_token'=>(!empty($access_token['oauth_token']) ? $access_token['oauth_token'] : ''),);
		$params['screen_name']=$user;

		$response = $this->oAuthRequest($this->followusURL, 'twitter_followus', $params,!empty($access_token['oauth_token_secret']) ? $access_token['oauth_token_secret']:null,true);
		$json_object = array();
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			if ($body) {
				$this->logit("postTweet:INFO:response:");
				$json_object = json_decode($this->json_pretty_print($body), true);
			}
		}

		$rtn = array();
		//if(!empty($json_object['error']) || empty($json_object['id_str']) || empty($json_object['screen_name']) || empty($json_object['following']) || $json_object['following']!=1) $rtn['error'] = 'error';
		if(!empty($json_object['error']) || empty($json_object['id_str']) || empty($json_object['screen_name'])) $rtn['error'] = 'error';
		elseif(empty($json_object['following']) || $json_object['following']!=1) {
			$response = $this->oAuthRequest($this->followusURL, 'twitter_followus', $params,!empty($access_token['oauth_token_secret']) ? $access_token['oauth_token_secret']:null,true);
			$json_object = array();
			if (!empty($response)) {
				list($info, $header, $body) = $response;
				if ($body) {
					$this->logit("postTweet:INFO:response:");
					$json_object = json_decode($this->json_pretty_print($body), true);
				}
			}
			if(!empty($json_object['error']) || empty($json_object['id_str']) || empty($json_object['screen_name']) || empty($json_object['following']) || $json_object['following']!=1) $rtn['error'] = 'error';
			else $rtn['id'] = $json_object['id_str'];
		}
		else $rtn['id'] = $json_object['id_str'];

		return $rtn;

	}

}
