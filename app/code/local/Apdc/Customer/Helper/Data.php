<?php

class Apdc_Customer_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCustomerWebsite()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $store = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore();
        return $store->getBaseUrl();
    }

}
