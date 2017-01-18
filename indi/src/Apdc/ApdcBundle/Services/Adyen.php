<?php

namespace Apdc\ApdcBundle\Services;

use Apdc\ApdcBundle\Services\AdyenLogs;

class Adyen {

	/* IP SERVOR 137.74.162.115/32	*/

	private $_ch;
	private $adyenlogs;

	public function __construct(AdyenLogs $adyenlogs){

		$this->adyenlogs = $adyenlogs;
		$this->_ch = curl_init();

		if($this->_ch === FALSE){
			throw new Exception('Adyen: curl_init() error:'.curl_error($this->_ch));
		}
		curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($this->_ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->_ch, CURLOPT_POST, true);
	}

	public function __destruct(){

		curl_close($this->_ch);
	}

	public function refund($value, $originalReference)
	{
		$refund_url			= $this->adyenlogs->getRefundUrl();
		$refund_webservice	= $this->adyenlogs->getRefundWebservice();
		$refund_password	= $this->adyenlogs->getRefundPassword();
	
		$refundTable = array(
			"merchantAccount" => "AuPasDeCoursesFR",
			"modificationAmount" => array(
				"value" => $value,
				"currency" => "EUR"
			),
			"originalReference" => $originalReference
		);
		$jsonRefundTable = json_encode($refundTable);

		curl_setopt($this->_ch, CURLOPT_URL, $refund_url);
		curl_setopt($this->_ch, CURLOPT_USERPWD, $refund_webservice.':'.$refund_password);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonRefundTable);
		$refundResult = curl_exec($this->_ch);

		if(curl_errno($this->_ch))
			throw new Exception('Refund Curl Error'.curl_error($this->_ch)); 

		$refundDecoded = json_decode($refundResult, true);
		if (!in_array("[refund-received]", $refundDecoded))
			throw new Exception('Adyen Error, Remboursement non valide');

		return($refundDecoded["pspReference"]); 
	}

	public function payout($value, $iban, $ownerName, $merchantAccount, $reference, $shopperEmail, $shopperReference)  
	{
		$store_submit_3party_url	= $this->adyenlogs->getStoreSubmit3PartyUrl();
		$store_payout_webservice	= $this->adyenlogs->getStorePayoutWebService();
		$store_payout_password		= $this->adyenlogs->getStorePayoutPassword();
		$confirm_3party_url			= $this->adyenlogs->getConfirm3PartyUrl();
		$review_payout_webservice	= $this->adyenlogs->getReviewPayoutWebservice();
		$review_payout_password		= $this->adyenlogs->getReviewPayoutPassword();

		$storeTable = array(
			"amount" => array(
				"currency" =>"EUR",
				"value" => $value
			),
			"bank" => array(
				"iban" => $iban,
				"ownerName" => $ownerName,
				"countryCode" => "FR"
			),
			"merchantAccount" => $merchantAccount,
			"recurring" => array(
				"contract" => "PAYOUT"
			),
			"reference" => $reference,
			"shopperEmail" => $shopperEmail,
			"shopperReference" => $shopperReference
		);
		$jsonStoreTable = json_encode($storeTable);

		curl_setopt($this->_ch, CURLOPT_URL, $store_submit_3party_url);
		curl_setopt($this->_ch, CURLOPT_USERPWD, $store_payout_webservice.':'.$store_payout_password);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonStoreTable);
		$storeResult = curl_exec($this->_ch);
		$storeDecoded = json_decode($storeResult, true);
		if (!in_array("[payout-submit-received]", $storeDecoded))
			throw new Exception('Adyen Error, Payout Store and Submit non valide');



		/* CONFIRMATION DU PAYOUT */
		$jsonDecoded = json_decode($storeResult, true);
		$jsonDecoded["merchantAccount"] = $merchantAccount;
		$jsonDecoded["originalReference"] = $jsonDecoded["pspReference"];
		unset($jsonDecoded["pspReference"]);
		unset($jsonDecoded["resultCode"]);

		$jsonEncoded = json_encode($jsonDecoded);

		curl_setopt($this->_ch, CURLOPT_URL, $confirm_3party_url);
		curl_setopt($this->_ch, CURLOPT_USERPWD, $review_payout_webservice.':'.$review_payout_password);

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonEncoded);

		$result = curl_exec($this->_ch);
		$storeDecoded = json_decode($result, true);
		if (!in_array("[payout-confirm-received]", $storeDecoded))
			throw new Exception('Adyen Error, Confirm non valide');

		return($storeDecoded["pspReference"]); 

	}
}
