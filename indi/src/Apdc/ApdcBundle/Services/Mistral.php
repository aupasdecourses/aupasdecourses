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
		/*
		$array = array(
			'Token' 		=> '6AA0A660A6B647C39F4CE6CED09621A2',
			'PartnerRef' 	=> 'APDC-5535',
			'OrderRef' 		=> '2017000293-272',
		 );
		*/

		$jsonArray = json_encode($array);

		curl_setopt($this->_ch, CURLOPT_URL, $this->stars_services_api_url);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonArray);

		$result = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
			throw new Exception('Refund Curl Error'.curl_error($this->_ch));

		$jsonResult = json_decode($result, true);

		return $jsonResult[];
	}
}