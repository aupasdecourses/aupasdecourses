<?php

namespace Apdc\ApdcBundle\Services;

class AdyenLogs{

/* 3RD PARTY PAYOUT */
protected $store_submit_3party_url			= 'https://pal-test.adyen.com/pal/servlet/Payout/v12/storeDetailAndSubmitThirdParty';
protected $store_payout_webservice			= 'storePayout_104791@Company.AuPasDeCourses';
protected $store_payout_password			= '9GsnR!sm3]*w7>rh%^bHSd!@2';

protected $confirm_3party_url				= 'https://pal-test.adyen.com/pal/servlet/Payout/v12/confirmThirdParty';
protected $decline_3party_url				= 'https://pal-test.adyen.com/pal/servlet/Payout/v12/declineThirdParty';
protected $review_payout_webservice			= 'reviewPayout_092607@Company.AuPasDeCourses';
protected $review_payout_password			= 'BZYi/(p3h<BTA<v13At3@Dcj*';

/* REFUND PAYIN */
protected $refund_url						= 'https://pal-test.adyen.com/pal/servlet/Payment/v18/refund';
protected $refund_webservice				= 'ws_224975@Company.AuPasDeCourses';
protected $refund_password					= 'J?nBNkZQtJp3zW-7>1{1nm?1/';

/* LIST RECURRING DETAILS */
protected $list_recurr_details_url			= 'https://pal-test.adyen.com/pal/servlet/Recurring/v18/listRecurringDetails';
protected $list_recurr_details_webservice	= 'ws_224975@Company.AuPasDeCourses';
protected $list_recurr_details_password		= 'J?nBNkZQtJp3zW-7>1{1nm?1/';


public function getStoreSubmit3PartyUrl(){
	return $this->store_submit_3party_url;
}
public function getStorePayoutWebService(){
	return $this->store_payout_webservice;
}
public function getStorePayoutPassword(){
	return $this->store_payout_password;
}


public function getConfirm3PartyUrl(){
	return $this->confirm_3party_url;
}
public function getDecline3PartyUrl(){
	return $this->decline_3party_url;
}
public function getReviewPayoutWebservice(){
	return $this->review_payout_webservice;
}
public function getReviewPayoutPassword(){
	return $this->review_payout_password;
}


public function getRefundUrl(){
	return $this->refund_url;
}
public function getRefundWebservice(){
	return $this->refund_webservice;
}
public function getRefundPassword(){
	return $this->refund_password;
}


public function getListRecurrDetailsUrl(){
	return $this->list_recurr_details_url;
}
public function getListRecurrDetailsWebservice(){
	return $this->list_recurr_details_webservice;
}
public function getListRecurrDetailsPassword(){
	return $this->list_recurr_details_password;
}

}
?>
