<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Mistral
{
	private $_ch;

	private $stars_services_api_url;
	private $stars_services_api_token;

	public function __construct($stars_services_api_url, $stars_services_api_token)
	{
		\Mage::app();	

		$this->_ch = curl_init();

		$this->stars_services_api_url	= $stars_services_api_url;
		$this->stars_services_api_token = $stars_services_api_token;

		if ($this->_ch === false) {
			throw new \Exception('Mistral: curl_init() error:'.curl_error($this->_ch));
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

	public function getPictures($partner_ref, $order_id, $merchant_id)
	{
		
		$array = array(
			 'Token' 		=> $this->stars_services_api_token,
			 'PartnerRef' 	=> $partner_ref,
			 'OrderRef' 	=> $order_id."-".$merchant_id,
		 );
		

		$json_array = json_encode($array);

		curl_setopt($this->_ch, CURLOPT_URL, $this->stars_services_api_url);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $json_array);

		$result = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
			throw new \Exception('Refund Curl Error'.curl_error($this->_ch));

		if(strstr($result,"Authentification invalide"))
			throw new \Exception('Authentification invalide');

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
			]);
		}
		return $data;
	}

	public function convert_base64_to_img($base64_string, $image_type, $order_id, $merchant_id)
	{
		$media_folder = $this->mediaPath().'/attachments/'.$order_id;
		
		if (!file_exists($media_folder)) {
			try {
				$oldmask = umask(0);
				mkdir($media_folder, 0777, true);
				umask($oldmask);
			} catch (Exception $e) { }
		}

		if (file_exists($media_folder)) {
		
			$img = "{$media_folder}/{$order_id}-{$merchant_id}.{$image_type}";

			$img_file = fopen($img, "w");
			fwrite($img_file, base64_decode($base64_string));
			fclose($img_file);

		}
	}	


}
