<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Oauth_Google extends AwoDev_AwoRewards_Helper_Oauth {

	public $requestTokenURL = 'https://accounts.google.com/o/oauth2/token';
	public $authorizeURL = 'https://accounts.google.com/o/oauth2/auth';
	public $accessTokenURL = 'https://www.google.com/accounts/OAuthGetAccessToken';
	public $contactsURL = 'https://www.google.com/m8/feeds/contacts/default/full';
	public $contactsScope = 'https://www.google.com/m8/feeds/';
	public $shortURL = 'https://www.googleapis.com/urlshortener/v1/url';
	
	public $debug = 0;
	
	function init($callback='') {
		
		$is_enabled = (int) Mage::getStoreConfig('awodev_aworewards/external_api/social_google_enabled');;
		if(!empty($is_enabled)) {
			$this->oauth_consumer_key = Mage::getStoreConfig('awodev_aworewards/external_api/social_google_key');
			$this->oauth_consumer_secret = Mage::getStoreConfig('awodev_aworewards/external_api/social_google_secret');
			$this->callback = $callback;
		}
	}
	

	function get_request_token($usePost=false, $passOAuthInHeader=false) {
		$params = array(
			'response_type'=>'code',
			'client_id'=>$this->oauth_consumer_key,
			'redirect_uri'=>$this->callback,
			'scope'=>$this->contactsScope,
			'access_type'=>'offline',
			'approval_prompt'=>'force',
		);

		$authUrl =  $this->authorizeURL.'?'.http_build_query($params);
		header('Location: '.$authUrl);
		exit;
	}
	
	function get_login_url($token, $sign_in_with_twitter = TRUE) { return; }


	function get_contacts ($access_token, $emails_count, $usePost=false, $passOAuthInHeader=true, $port=443) {
		$authorization_code = $access_token;
		$access_token = array();
		{ // get the accesstoken
			$usePost = true;
			$passOAuthInHeader = false;
			$returnFullResponse = true;
			$this->extra_params = array(
				'code'=> $authorization_code,
				'client_id'=> $this->oauth_consumer_key,
				'client_secret'=> $this->oauth_consumer_secret,
				'redirect_uri'=> $this->callback,
				'grant_type'=>'authorization_code',
			);
			$rtn = parent::get_request_token($usePost,$passOAuthInHeader,$returnFullResponse);
			$this->extra_params = array();
			if(!empty($rtn)) {
				$response = json_decode($rtn[2]);
				if(!empty($response->access_token)) $access_token['oauth_token'] = $response->access_token;
			}
		}	
		if(empty($access_token)) return;
		
		

		{ // retrieve contacts from google
			$url = $this->contactsURL.'?max-results='.$emails_count.'&oauth_token='.$access_token['oauth_token'];
			$xmlresponse = $this->_curl_file_get_contents($url);
			//echo '<textarea>'.$xmlresponse.'</textarea>';exit;
			if((strlen(stristr($xmlresponse,'Authorization required'))>0) && (strlen(stristr($xmlresponse,'Error '))>0)) {
				echo "<h2>OOPS !! Something went wrong. Please try reloading the page.</h2>";
				exit();
			}
			$xml = new SimpleXMLElement($xmlresponse);
			$xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
			$result = $xml->xpath('//gd:email');
		}
		if(empty($result)) return;
		
		{ // format contacts
			$contacts = array();
			foreach($result as $item) {
				$email = (string)$item->attributes()->address;
				//echo $email;exit;
				$contacts[$email] = array('name'=>'','email'=>$email,);
			}
		}
		return $contacts;
	}
	
	function get_urlshort($long_url) {

		$query_parameter_string = json_encode(array('key'=>Mage::getStoreConfig('awodev_aworewards/external_api/social_google_apikey'),'longUrl'=>$long_url));
		//$response = $this->do_post($this->shortURL, $query_parameter_string, 443,array('Content-type:application/json'));
		$response = $this->do_post($this->shortURL.'?key='.Mage::getStoreConfig('awodev_aworewards/external_api/social_google_apikey'), $query_parameter_string, 443,array('Content-type:application/json'));
		$json_object = array();
		if (!empty($response)) {
			list($info, $header, $body) = $response;
			if ($body) {
				$this->logit("urlshort:INFO:response:");
				$json_object = json_decode($this->json_pretty_print($body), true);
			}
		}
		
		return !empty($json_object['id']) ? $json_object['id'] : '';			
	}

	function _curl_file_get_contents($url) {
		$curl = curl_init();
		//$userAgent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)';

		curl_setopt($curl,CURLOPT_URL,$url); //The URL to fetch. This can also be set when initializing a session with curl_init().
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,TRUE); //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
		curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,5); //The number of seconds to wait while trying to connect.

		//curl_setopt($curl, CURLOPT_USERAGENT, $userAgent); //The contents of the "User-Agent: " header to be used in a HTTP request.
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE); //To follow any "Location: " header that the server sends as part of the HTTP header.
		curl_setopt($curl, CURLOPT_AUTOREFERER, TRUE); //To automatically set the Referer: field in requests where it follows a Location: redirect.
		curl_setopt($curl, CURLOPT_TIMEOUT, 10); //The maximum number of seconds to allow cURL functions to execute.
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //To stop cURL from verifying the peer's certificate.

		$contents = curl_exec($curl);
		curl_close($curl);
		return $contents;
	}

}
