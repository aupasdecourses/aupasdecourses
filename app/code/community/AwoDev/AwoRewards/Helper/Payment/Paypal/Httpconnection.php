<?php
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Payment_Paypal_Httpconnection extends AwoDev_AwoRewards_Helper_Payment_Paypal {

	private $httpConfig;

	/**
	 * HTTP status codes for which a retry must be attempted
	 */
	private static $retryCodes = array('401', '403', '404', );

	private $logger;

	public function init($httpConfig)
	{
		if( !function_exists("curl_init") ) {
			throw new Exception ("Curl module is not available on this system");
		}
		$this->httpConfig = $httpConfig;
		$this->logger = Mage::helper('awodev_aworewards/payment_paypal_log');
	}	

	private function getHttpHeaders() {
		
		$ret = array();
		foreach($this->httpConfig->getHeaders() as $k=>$v) {
			$ret[] = "$k: $v";
		}
		return $ret;
	}
	
	/**
	 * Executes an HTTP request
	 *
	 * @param string $data query string OR POST content as a string
	 * @throws PPConnectionException
	 */
	public function execute($data) {
		$this->logger->fine("Connecting to " . $this->httpConfig->getUrl());			
		$this->logger->fine("Payload " . $data);

		$ch = curl_init($this->httpConfig->getUrl());
		curl_setopt_array($ch, $this->httpConfig->getCurlOptions());		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $this->httpConfig->getUrl());
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getHttpHeaders());		
		foreach($this->getHttpHeaders() as $header) {
			//TODO: Strip out credentials and other secure info when logging.
			$this->logger->info("Adding header $header");
		}
		if($this->httpConfig->getMethod()) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->httpConfig->getMethod());
		}

		$result = curl_exec($ch);
		if (curl_errno($ch) == 60) {
		 	$this->logger->info("Invalid or no certificate authority found - Retrying using bundled CA certs file");
		 	curl_setopt($ch, CURLOPT_CAINFO,
		 	dirname(__FILE__) . '/cacert.pem');
		 	$result = curl_exec($ch);
		}
		$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$retries = 0;
		if(in_array($httpStatus, self::$retryCodes) && $this->httpConfig->getRetryCount() != null) {
			$this->logger->info("Got $httpStatus response from server. Retrying");

			do 	{
				$result = curl_exec($ch);
				$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			} while (in_array($httpStatus, self::$retryCodes) && ++$retries < $this->httpConfig->getRetryCount() );


		}
		if ( curl_errno($ch) ) {
			curl_close($ch);
			throw new Exception($this->httpConfig->getUrl().' '.curl_error($ch).' '.curl_errno($ch));
		}

		curl_close($ch);

		if(in_array($httpStatus, self::$retryCodes))
		{
			throw new Exception($url ,"Retried ".$retries." times, Http Response code ".$httpStatus);
		}
		return $result;
	}

}
