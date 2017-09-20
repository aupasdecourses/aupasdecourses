<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Mistral
{
	private $_ch;

	private $stars_services_api_url;

	public function __construct($stars_services_api_url)
	{
		\Mage::app();	

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

	private function mediaPath()
    {
        return realpath(__DIR__.'/../../../../../media');
    }

	public function getPictures($token, $partner_ref, $order_id, $merchant_id)
	{
		
		$array = array(
			 'Token' 		=> $token,
			 'PartnerRef' 	=> $partner_ref,
			 'OrderRef' 	=> $order_id."-".$merchant_id,
		 );
		

		$json_array = json_encode($array);

		curl_setopt($this->_ch, CURLOPT_URL, $this->stars_services_api_url);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $json_array);

		$result = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
			throw new Exception('Refund Curl Error'.curl_error($this->_ch));

		$json_result = json_decode($result, true);

		return $json_result;

	}

	public function getApdcNeighborhood()
	{
		$neighborhood = \Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
		$data = [];

		foreach ($neighborhood as $neigh) {
			array_push($data, [
				'store_name'	=> $neigh->getData('name'),
				'store_id'		=> $neigh->getData('website_id'),
				'partner_ref'	=> $neigh->getData('code_do'),
				'store_token'	=> $neigh->getData('mistral_guid'),
			]);
		}
		return $data;
	}

	public function convert_base64_to_img($base64_string, $order_id, $merchant_id)
	{
		$media_folder = $this->mediaPath().'/merchants_tickets/'.$order_id;
		
		if (!file_exists($media_folder)) {
			try {
				$oldmask = umask(0);
				mkdir($media_folder, 0777, true);
				umask($oldmask);
			} catch (Exception $e) { }
		}

		if (file_exists($media_folder)) {
			$img_folder	= "{$media_folder}/{$order_id}";
			$img_name	= "{$order_id}-{$merchant_id}";
		}

		$img_f = fopen($img_folder, "w");
		fwrite($img_f, base64_decode($base64_string));
		fclose($img_f);

	}	


}
