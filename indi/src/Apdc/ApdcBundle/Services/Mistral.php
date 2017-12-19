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
				//on devrait utiliser le type d'image automatiquement mais cette fonction a été codée dans une focnction lancée après ... A refactoriser!!!
				$results['ticket_com'] .= ";{$results['order_id']}/{$results['order_id']}-{$merchant_id}.jpeg";
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

	/**
	 * FONCTIONS POUR PROCESS CODE COULEURS HORAIRES ENLEVEMENTS LIVRAISONS MISTRAL
	 */

	public function constructMistralDeliveryResults($orders) 
	{
		$neighborhood = $this->getApdcNeighborhood();
		$results = [];

		foreach ($neighborhood as $neigh) {
			foreach ($orders as $order_id => $order) {
				if ($neigh['store_id'] == $order['store_id']) {
					$results[$order_id] = [
						'partner_ref'			=> $neigh['partner_ref'],
						'merchant_id'			=> [],
						'real_hour_picking'		=> '',
						'slot_start_picking'	=> '',
						'slot_end_picking'		=> '',
						'real_hour_shipping'	=> '',
						'slot_start_shipping'	=> '',
						'slot_end_shipping'		=> '',
					];

					foreach ($order['products'] as $product) {
						$results[$order_id]['merchant_id'][] = $product['commercant_id'];
					}

					$results[$order_id]['merchant_id'] = array_unique($results[$order_id]['merchant_id']);
				}
			}		
		}
		
		return $results;
	}

	public function cleanTempMistralDeliveryData($temp)
	{
		foreach ($temp as $order_id => $tmp) {
			foreach ($tmp as $merch_id => $mistral_result) {
				if (is_numeric($merch_id)) {
					if (isset($mistral_result['Message'])) {
						unset($temp[$order_id]); // Si commande inconnue
					}
					if ($mistral_result['StatusCode'] == 'EA' || $mistral_result['StatusCode'] == 'EEC') {
						unset($temp[$order_id][$merch_id]); // Si en acheminement ou en enlevement
					}
				}
			}

			if (empty($temp[$order_id])) {
				unset($temp[$order_id]); // Si manque infos Mistral
			}
		}	

		return $temp;
	}

	public function storeMistralDeliveryResults($results, $temp)
	{
		foreach ($results as $order_id => $result) {
			foreach ($temp as $o_id => $tmp) {
				foreach ($tmp as $merch_id => $res) {
					if ($order_id == $o_id) {
						$results[$order_id]['real_hour_picking']	= $res['Pick']['RealHour'];
						$results[$order_id]['slot_start_picking']	= $res['Pick']['SlotStart'];
						$results[$order_id]['slot_end_picking']		= $res['Pick']['SlotEnd'];
						$results[$order_id]['real_hour_shipping']	= $res['Delivery']['RealHour'];
						$results[$order_id]['slot_start_shipping']	= $res['Delivery']['SlotStart'];
						$results[$order_id]['slot_end_shipping']	= $res['Delivery']['SlotEnd'];

					}
				}
			}	

			unset($results[$order_id]['partner_ref'], $results[$order_id]['merchant_id']);
		}

		return $results;	
	}

}
