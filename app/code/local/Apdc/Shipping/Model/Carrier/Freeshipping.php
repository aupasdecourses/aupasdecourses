<?php

class Apdc_Shipping_Model_Carrier_Freeshipping extends Mage_Shipping_Model_Carrier_Freeshipping
{

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return parent::collectRates($request);
    }

}
