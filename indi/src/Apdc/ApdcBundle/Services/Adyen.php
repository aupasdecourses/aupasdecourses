<?php

class Adyen {
	/* IP SERVOR 137.74.162.115/32	*/

	private $_ch;

	public function __construct(){

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

	public function refund($merchantAccount, $value, $originalReference, $reference)
	{
		$refundTable = array(
			"merchantAccount" => $merchantAccount,
			"modificationAmount" => array(
				"value" => $value,
				"currency" => "EUR"
			),
			"originalReference" => $originalReference,
			"reference" => $reference
		);
		$jsonRefundTable = json_encode($refundTable);

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payment/v18/refund");
		curl_setopt($this->_ch, CURLOPT_USERPWD,"ws_224975@Company.AuPasDeCourses:N@%n^IStD?2Xc4ISkc[>r@7<g");
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonRefundTable);
		$refundResult = curl_exec($this->_ch);

		if(curl_errno($this->_ch)){
			throw new Exception('Refund Curl Error'.curl_error($this->_ch)); 
		}
		$refundDecoded = json_decode($refundResult, true);
		if (!in_array("[refund-received]", $refundDecoded))
			throw new Exception('Adyen Error, Remboursement non valide');

		return($refundDecoded["pspReference"]); 
	}

	public function payout($value, $iban, $ownerName, $merchantAccount, $reference, $shopperEmail, $shopperReference)  
	{
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

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/storeDetailAndSubmitThirdParty");
		curl_setopt($this->_ch, CURLOPT_USERPWD, "storePayout_104791@Company.AuPasDeCourses:9GsnR!sm3]*w7>rh%^bHSd!@2");
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

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/confirmThirdParty");
		curl_setopt($this->_ch, CURLOPT_USERPWD, "reviewPayout_092607@Company.AuPasDeCourses:BZYi/(p3h<BTA<v13At3@Dcj*");

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonEncoded);

		$result = curl_exec($this->_ch);
		$storeDecoded = json_decode($result, true);
		if (!in_array("[payout-confirm-received]", $storeDecoded))
			throw new Exception('Adyen Error, Confirm non valide');

		return($storeDecoded["pspReference"]); 

	}
}
