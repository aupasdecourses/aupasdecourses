<?php

class Adyen {

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


	public function refundCurlAction() {

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payment/v18/refund");
		curl_setopt($this->_ch, CURLOPT_USERPWD,"");

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonRefund);

		$result = curl_exec($this->_ch);

		if(curl_errno($this->_ch)){
			print "Error: ". curl_error($this->_ch);
		} else {
			var_dump($result);
		}

	}

	public function payoutCurlStoreAction(
		$amount = array($currency, $value),
		$bank = array($iban, $ownerName, $countryCode),
		$merchantAccount,
		$recurring = array($contract),
		$reference, $shopperEmail, $shopperReference																
	)  
	{

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/storeDetailAndSubmitThirdParty");
		curl_setopt($this->_ch, CURLOPT_USERPWD, "storePayout_104791@Company.AuPasDeCourses:9GsnR!sm3]*w7>rh%^bHSd!@2");

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonStore);

		$result = curl_exec($this->_ch);

		if(curl_errno($this->_ch)) {
			print "Error: ". curl_error($this->_ch);
		} else {
			var_dump($result);
		}
		return ($id);
	}

	public function payoutCurlConfirmAction($id) {

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/confirmThirdParty");
		curl_setopt($this->_ch, CURLOPT_USERPWD, "reviewPayout_092607@Company.AuPasDeCourses:BZYi/(p3h<BTA<v13At3@Dcj*");

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonConfirm);

		$result = curl_exec($this->_ch);

		if(curl_errno($this->_ch)) {
			print "Error: ". curl_error($this->_ch);
		} else {
			var_dump($result);
		}
	}
	/*
	public function payoutCurlDeclineAction() {

		curl_setopt($this->_ch, CURLOPT_URL, "https://pal-test.adyen.com/pal/servlet/Payout/v12/declineThirdParty");
		curl_setopt($this->_ch, CURLOPT_USERPWD, "reviewPayout_092607@Company.AuPasDeCourses:BZYi/(p3h<BTA<v13At3@Dcj*");

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonDecline);

		$result = curl_exec($this->_ch);

		if(curl_errno($this->_ch)) {
			print "Error: ". curl_error($this->_ch);
		} else {
			var_dump($result);
		}
	}*/
}
