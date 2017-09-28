<?php

class Apdc_Delivery_Model_Adyen_Event extends Adyen_Payment_Model_Event{

    const ADYEN_EVENT_PAYOUT_THIRDPARTY = 'PAYOUT_THIRDPARTY';

    public function getPayoutConstant(){
    	return self::ADYEN_EVENT_PAYOUT_THIRDPARTY;
    }
    
}