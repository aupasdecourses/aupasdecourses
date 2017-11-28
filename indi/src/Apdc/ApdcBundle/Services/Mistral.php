<?php

namespace Apdc\ApdcBundle\Services;

include_once '../../app/Mage.php';

class Mistral
{
	private $_ch;

	private $stars_services_api_getorderwarehouse_url;
	private $stars_services_api_getpictures_url;
	private $stars_services_api_token;

	public function __construct($stars_services_api_getorderwarehouse_url, $stars_services_api_getpictures_url, $stars_services_api_token)
	{
		\Mage::app();	

		$this->_ch = curl_init();

		$this->stars_services_api_getorderwarehouse_url		= $stars_services_api_getorderwarehouse_url;
		$this->stars_services_api_getpictures_url			= $stars_services_api_getpictures_url;
		$this->stars_services_api_token						= $stars_services_api_token;

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

	public function getOrderWarehouse($partner_ref, $order_id, $merchant_id)
	{
		$array = array(
			'Token'			=> $this->stars_services_api_token,
			'PartnerRef'	=> $partner_ref,
			'OrderRef'		=> $order_id."-".$merchant_id,
		);

		$json_array = json_encode($array);

		curl_setopt($this->_ch, CURLOPT_URL, $this->stars_services_api_getorderwarehouse_url);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $json_array);

		$result = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
			throw new \Exception('Refund Curl Error'.curl_error($this->_ch));

		if(strstr($result,"Authentification invalide"))
			throw new \Exception('Authentification invalide');

		$json_result = json_decode($result, true);

		return $json_result;	
	}

	public function getPictures($partner_ref, $order_id, $merchant_id)
	{
		
		$array = array(
			 'Token' 		=> $this->stars_services_api_token,
			 'PartnerRef' 	=> $partner_ref,
			 'OrderRef' 	=> $order_id."-".$merchant_id,
		 );
		

		$json_array = json_encode($array);

		curl_setopt($this->_ch, CURLOPT_URL, $this->stars_services_api_getpictures_url);
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

	/**
	 *	FONCTIONS POUR PROCESS AUTO UPLOAD TICKET REMBOURSEMENT MISTRAL
	 */

	public function constructMistralImgsResults($order, $id)
	{
		$neighborhood = $this->getApdcNeighborhood();

		$results = [
			'order_id' => $id,
			'order_mid' => $order[-1]['order']['mid'],
			'ticket_com' => '',
			'partner_ref' => '',
		];		

		foreach ($order as $merchant_id => $merchant) {
			if (is_numeric($merchant_id) && $merchant_id != -1) {
				foreach ($neighborhood as $neigh) {
					if ($neigh['store_id'] == $merchant['merchant']['store_id']) {
						$results['partner_ref'] = $neigh['partner_ref'];
						$results[$merchant_id] = '';
					}
				}
			}
		}

		foreach ($results as $merchant_id => $result) {
			if (is_numeric($merchant_id)) {
				$results['ticket_com'] .= ";{$results['order_id']}/{$results['order_id']}-{$merchant_id}";
			}
		}
		$results['ticket_com'] = substr($results['ticket_com'], 1);

		return $results;
	}	

	
	public function storeMistralImgsResults($temp, $results)
	{
		foreach ($temp as $merchant_id => $data) {
			if ($data['AsPicture'] == true) {
				foreach ($data['Pictures'] as $type => $content) {
					if ($content['MoveTypeCode'] == 'E') {
						$results[$merchant_id]['base64_string'] = $content['ImageBase64'];
						$results[$merchant_id]['image_type'] = substr($content['ImageType'], 6);
					} else {
						unset($results[$merchant_id]); // Si ticket == signature du commercant
					}
				}
			} else {
				unset($results[$merchant_id]); // Si 0 ticket commercant
			}
		}

		return $results;
	}
}
