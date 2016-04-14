<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Payment_Paypal_Apiservice extends AwoDev_AwoRewards_Helper_Payment_Paypal {
	
	public $endpoint;
	public $serviceName;
	private $logger;
	private $handlers = array();
	private $serviceBinding;

	public function init($port, $serviceName, $serviceBinding, $handlers=array()) {
		$env_prefix = Mage::getStoreConfig('awodev_aworewards/payout_paypal/sandbox') ? 'sandbox' : 'paypal';
		$this->config = array (
			'acct1.UserName' => Mage::getStoreConfig('awodev_aworewards/payout_paypal/'.$env_prefix.'_username'),
			'acct1.Password' => Mage::getStoreConfig('awodev_aworewards/payout_paypal/'.$env_prefix.'_password'),
			'acct1.Signature' => Mage::getStoreConfig('awodev_aworewards/payout_paypal/'.$env_prefix.'_signature'),
			'acct1.AppId' => 'APP-80W284485P519543T',
			'http.ConnectionTimeOut' => 30,
			'http.Retry' => 5,
			'service.RedirectURL' => 'https://www.sandbox.paypal.com/webscr&cmd=',
			'service.DevCentralURL' => 'https://developer.paypal.com',
			'service.EndPoint.IPN' => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
			'service.EndPoint.AdaptivePayments' => 'https://svcs.sandbox.paypal.com/',
			'log.FileName' => 'PayPal.log',
			'log.LogLevel' => 'INFO',
			'log.LogEnabled' => 0,
		);
		
		if(!Mage::getStoreConfig('awodev_aworewards/payout_paypal/sandbox')) {
			$this->config['service.RedirectURL'] = 'https://www.paypal.com/webscr&cmd=';
			$this->config['service.EndPoint.IPN'] = 'https://ipnpb.paypal.com/cgi-bin/webscr';
			$this->config['service.EndPoint.AdaptivePayments'] = 'https://svcs.paypal.com/';
		}

		
		
		$this->serviceName = $serviceName;
		$this->endpoint = $this->config['service.EndPoint.'.$port];
		
		$this->logger = Mage::helper('awodev_aworewards/payment_paypal_log');
		$this->handlers = $handlers;
		$this->serviceBinding = $serviceBinding;
		

		
		
	}

	public function setServiceName($serviceName) {
		$this->serviceName = $serviceName;
	}

	public function addHandler($handler) {
		$this->handlers[] = $handler;
	}

	public function makeRequest($apiMethod, $params, $apiUsername = null, $accessToken = null, $tokenSecret = null) {

		if(is_string($apiUsername) || is_null($apiUsername)) {
			// $apiUsername is optional, if null the default account in config file is taken
			$apiCredential = $this->initCredential($apiUsername);
		} else {
			$apiCredential = $apiUsername; //TODO: Aargh
		}
		
		if($this->serviceBinding == 'SOAP' ) {
			$url = $this->endpoint;
		} else {
			$url = $this->endpoint . $this->serviceName . '/' . $apiMethod;
		}

		//pprequest
		$request = new stdclass;
		$request->requestObject = $params;
		$request->credential = $apiCredential;
		$request->bindingType = $this->serviceBinding;
		$request->bindingInfo = array();

		$httpConfig = Mage::helper('awodev_aworewards/payment_paypal_httpconfig');
		$httpConfig->init($url,'POST');
		$httpConfig->handle($request);

		$payload = $request->requestObject->toNVPString();
		$connection = $this->getConnection($httpConfig);
		$this->logger->info("Request: $payload");
		$response = $connection->execute($payload);
		$this->logger->info("Response: $response");
		
		return array('request' => $payload, 'response' => $response);
	}
	
	public function getConnection($httpConfig) {
		if( isset($this->config["http.ConnectionTimeOut"]) ) {
			$httpConfig->setHttpTimeout( $this->config["http.ConnectionTimeOut"] );
		}
		if( isset($this->config["http.Proxy"])) {
			$httpConfig->setHttpProxy( $this->config["http.Proxy"] );
		}
		if( isset($this->config["http.Retry"]) ) {
			$httpConfig->setHttpRetryCount($this->config["http.Retry"] ) ;
		}
		
		$connection = Mage::helper('awodev_aworewards/payment_paypal_httpconnection');
		$connection->init($httpConfig);
		return $connection;
	}
	
	
	
	private function initCredential($userId){
		$suffix = 1;
		$prefix = "acct";

		$arrayPartKeys = array();
		foreach ($this->config as $key => $value) {
			$pos = strpos($key, '.');
			if(strstr($key, "acct")){
				$arrayPartKeys[] = substr($key, 0, $pos);
			}
		}
		$arrayPartKeys = array_unique($arrayPartKeys);

		if(count($arrayPartKeys) == 0)
			throw new Exception("No valid API accounts have been configured");

		$key = $prefix.$suffix;
		$credentialHashmap = array();
		while (in_array($key, $arrayPartKeys)){
							
			if(isset($this->config[$key.".Signature"]) 
					&& $this->config[$key.".Signature"] != null && $this->config[$key.".Signature"] != ""){
					
				$userName = isset($this->config[$key.'.UserName']) ? $this->config[$key.'.UserName'] : "";
				$password = isset($this->config[$key.'.Password']) ? $this->config[$key.'.Password'] : "";
				$signature = isset($this->config[$key.'.Signature']) ? $this->config[$key.'.Signature'] : "";
				
				$sigcred = new stdclass;
				$sigcred->userName = $userName;
				$sigcred->password = $password;
				$sigcred->signature = $signature;
				$sigcred->applicationId = $this->config[$key.'.AppId'];


				$credentialHashmap[$userName] = $sigcred;
			} 
			$suffix++;
			$key = $prefix.$suffix;
		}

		if($userId == null) $credObj = $credentialHashmap[$this->config['acct1.UserName']];
		else if (array_key_exists($userId, $credentialHashmap)) $credObj = $credentialHashmap[$userId];
			
		if (empty($credObj)) {
			throw new Exception("Invalid userId $userId");
		}
		return $credObj;
	}


}
