<?php

namespace Apdc\ApdcBundle\Services;

class Mistral
{
	private $_ch;

	private $stars_services_api_url;

	public function __construct($stars_services_api_url)
	{
		$this->_ch = curl_init();

		$this->stars_services_api_url = $stars_services_api_url;

		if ($this->_ch === false) {
			throw new Exception('Mistral: curl_init() error:'.curl_error($this->_ch));
		}
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($this->_ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->_ch, CURLOPT_POST, true);
	}

	public function __destruct()
	{
		curl_close($this->_ch);
	}

	public function getPictures()
	{
		
		/* exemple avec pascal bassard */
		$array = array(
			// 'Token' 		=> 'APDC2712A9B6',
			// 'PartnerRef' 	=> 'APDC5535',
			// 'OrderRef' 		=> '2017000293-272',
		 );
		

		$jsonArray = json_encode($array);

		curl_setopt($this->_ch, CURLOPT_URL, $this->stars_services_api_url);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonArray);

		$result = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
			throw new Exception('Refund Curl Error'.curl_error($this->_ch));

		$jsonResult = json_decode($result, true);

		return $jsonResult;
	}
}