<?php 
/**
 * @package		AwoDev_AwoRewards
 * @copyright	Copyright (C) Seyi Awofadeju - All rights reserved.
 * @website		http://awodev.com
 * @license		http://awodev.com/content/magento-license
 **/

class AwoDev_AwoRewards_Helper_Payment_Paypal_Service_Adaptive extends AwoDev_AwoRewards_Helper_Payment_Paypal_Service {

	// Service Version
	private static $SERVICE_VERSION = "1.8.2";

	// Service Name
	private static $SERVICE_NAME = "AdaptivePayments";

    // SDK Name
	protected static $SDK_NAME = "adaptivepayments-php-sdk";
	
	// SDK Version
	protected static $SDK_VERSION = "2.2.98";

	public function __construct() {
		parent::init(self::$SERVICE_NAME, 'NV', array('PPPlatformServiceHandler'));
        parent::$SDK_NAME    = self::$SDK_NAME ;
        parent::$SDK_VERSION = self::$SDK_VERSION;
	}




	/**
	 * Service Call: PaymentDetails
	 * @param PaymentDetailsRequest $paymentDetailsRequest
	 * @param mixed $apiCredential - Optional API credential - can either be
	 * 		a username configured in sdk_config.ini or a ICredential object
	 *      created dynamically 		
	 * @return PaymentDetailsResponse
	 * @throws APIException
	 */
	public function PaymentDetails($paymentDetailsRequest, $apiCredential = NULL) {
	
		$paymentDetailsResponse = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
		$paymentDetailsResponse->initvars(array(
			'responseEnvelope'=>null,
			'cancelUrl'=>null,
			'currencyCode'=>null,
			'ipnNotificationUrl'=>null,
			'memo'=>null,
			'paymentInfoList'=>null,
			'returnUrl'=>null,
			'senderEmail'=>null,
			'status'=>null,
			'trackingId'=>null,
			'payKey'=>null,
			'actionType'=>null,
			'feesPayer'=>null,
			'reverseAllParallelPaymentsOnError'=>null,
			'preapprovalKey'=>null,
			'fundingConstraint'=>null,
			'sender'=>null,
			'error'=>null,
		));
		foreach($paymentDetailsResponse->thisvars as $k=>$v) $paymentDetailsResponse->{$k} = $v;

		$resp = $this->call('AdaptivePayments', 'PaymentDetails', $paymentDetailsRequest, $apiCredential);
		
		$paymentDetailsResponse->init(Mage::helper('awodev_aworewards/payment_paypal_utils')->nvpToMap($resp));
		return $paymentDetailsResponse;
	}
	 

	/**
	 * Service Call: Pay
	 * @param PayRequest $payRequest
	 * @param mixed $apiCredential - Optional API credential - can either be
	 * 		a username configured in sdk_config.ini or a ICredential object
	 *      created dynamically 		
	 * @return PayResponse
	 * @throws APIException
	 */
	public function Pay($payRequest, $apiCredential = NULL) {

		$resp = $this->call('AdaptivePayments', 'Pay', $payRequest, $apiCredential);
		$payResponse = clone(Mage::helper('awodev_aworewards/payment_paypal_message'));
		$payResponse->initvars(array(
			'responseEnvelope'=>null,
			'payKey'=>null,
			'paymentExecStatus'=>null,
			'payErrorList'=>null,
			'defaultFundingPlan'=>null,
			'warningDataList'=>null,
			'error'=>null,
		));
		foreach($payResponse->thisvars as $k=>$v) $payResponse->{$k} = $v;
		$payResponse->init(Mage::helper('awodev_aworewards/payment_paypal_utils')->nvpToMap($resp));
		return $payResponse;
	}
	 

 
 
}