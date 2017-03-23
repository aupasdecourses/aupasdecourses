<?php

namespace Apdc\ApdcBundle\Services;

class Adyen
{
	private $_ch;

	/* Refund Pay-in */
	private $refund_url;
	private $refund_webservice;
	private $refund_password;

	/* 3rd party Pay-out */
	private $store_submit_3party_url;
	private $store_payout_webservice;
	private $store_payout_password;

	private $confirm_3party_url;
	private $decline_3party_url;
	private $review_payout_webservice;
	private $review_payout_password;

	/* List Recurring Details */
	private $list_recurr_details_url;
	private $list_recurr_details_webservice;
	private $list_recurr_details_password;

	public function __construct($refund_url, $refund_webservice, $refund_password, $store_submit_3party_url, $store_payout_webservice, $store_payout_password, $confirm_3party_url, $decline_3party_url, $review_payout_webservice, $review_payout_password, $list_recurr_details_url, $list_recurr_details_webservice, $list_recurr_details_password)
	{
		$this->_ch = curl_init();

		$this->refund_url						= $refund_url;
		$this->refund_webservice				= $refund_webservice;
		$this->refund_password					= $refund_password;
		$this->store_submit_3party_url			= $store_submit_3party_url;
		$this->store_payout_webservice			= $store_payout_webservice;
		$this->store_payout_password			= $store_payout_password;
		$this->confirm_3party_url				= $confirm_3party_url;
		$this->decline_3party_url				= $decline_3party_url;
		$this->review_payout_webservice			= $review_payout_webservice;
		$this->review_payout_password			= $review_payout_password;
		$this->list_recurr_details_url			= $list_recurr_details_url;
		$this->list_recurr_details_webservice	= $list_recurr_details_webservice;
		$this->list_recurr_details_password		= $list_recurr_details_password;

		if ($this->_ch === false) {
			throw new Exception('Adyen: curl_init() error:'.curl_error($this->_ch));
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

	/**	Soumet & effectue un remboursement client Adyen en CURL
	 *	Prend comme paramètre : un compte marchand (AuPasDeCoursesFR), un montant, une reference unique PSP
	 *	Un FlashBag Symfony est retourné en cas de succès
	 */
	public function refund($value, $originalReference)
	{
		$refundTable = array(
			"merchantAccount"		=> "AuPasDeCoursesFR",
			"modificationAmount"	=> array(
				"value"					=> $value,
				"currency"				=> "EUR"
			),
			"originalReference"		=> $originalReference
		);
		$jsonRefundTable = json_encode($refundTable);

		curl_setopt($this->_ch, CURLOPT_URL, $this->refund_url);
		curl_setopt($this->_ch, CURLOPT_USERPWD, $this->refund_webservice.':'.$this->refund_password);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonRefundTable);
		$refundResult = curl_exec($this->_ch);

		if (curl_errno($this->_ch))
			throw new Exception('Refund Curl Error'.curl_error($this->_ch));

		$refundDecoded = json_decode($refundResult, true);
		if (!in_array("[refund-received]", $refundDecoded))
			throw new Exception('Adyen Error, Remboursement non valide');

		return($refundDecoded['response']);
	}

	/**	Soumet & effectue un payout commercant Adyen en CURL
	 *	Prend comme paramètre : un compte marchand (AuPasDeCoursesFR), un montant,
	 *	des infos commercants (son IBAN, nom, ...), une reference (style 201702-22), des infos shoppers
	 */
	public function payout($value, $iban, $ownerName, $reference, $shopperEmail, $shopperReference)
	{
		$storeTable = array(
			"amount" => array(
				"currency"			=> "EUR",
				"value"				=> $value
			),
			"bank" => array(
				"iban"				=> $iban,
				"ownerName"			=> $ownerName,
				"countryCode"		=> "FR"
			),
			"merchantAccount"	=> "AuPasDeCoursesFR",
			"recurring"			=> array(
				"contract"			=> "PAYOUT"
			),
			"reference"			=> $reference,
			"shopperEmail"		=> $shopperEmail,
			"shopperReference"	=> $shopperReference
		);
		$jsonStoreTable = json_encode($storeTable);

		curl_setopt($this->_ch, CURLOPT_URL, $this->store_submit_3party_url);
		curl_setopt($this->_ch, CURLOPT_USERPWD, $this->store_payout_webservice.':'.$this->store_payout_password);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonStoreTable);
		$storeResult = curl_exec($this->_ch);
		$storeDecoded = json_decode($storeResult, true);
		if (!in_array("[payout-submit-received]", $storeDecoded))
			throw new Exception('Adyen Error, Payout Store and Submit non valide');



		/* CONFIRMATION DU PAYOUT */
		$jsonDecoded = json_decode($storeResult, true);
		$jsonDecoded["merchantAccount"]		= "AuPasDeCoursesFR";
		$jsonDecoded["originalReference"]	= $jsonDecoded["pspReference"];
		unset($jsonDecoded["pspReference"]);
		unset($jsonDecoded["resultCode"]);

		$jsonEncoded = json_encode($jsonDecoded);

		curl_setopt($this->_ch, CURLOPT_URL, $this->confirm_3party_url);
		curl_setopt($this->_ch, CURLOPT_USERPWD, $this->review_payout_webservice.':'.$this->review_payout_password);

		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $jsonEncoded);

		$result = curl_exec($this->_ch);
		$storeDecoded = json_decode($result, true);
		if (!in_array("[payout-confirm-received]", $storeDecoded))
			throw new Exception('Adyen Error, Confirm non valide');

		return($storeDecoded["pspReference"]);
	}
}
