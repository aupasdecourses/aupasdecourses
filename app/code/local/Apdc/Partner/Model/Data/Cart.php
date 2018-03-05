<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Model_Data_Cart 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Data_Cart extends Apdc_Partner_Model_Data
{
    protected $cartData = [];

    /**
     * setCartData 
     * 
     * @param array $data data 
     * 
     * @return this
     */
    public function setCartData($data)
    {
        $this->cartData = $data;
        return $this;
    }

    /**
     * getCartData 
     * 
     * @return array
     */
    public function getCartData()
    {
        return $this->cartData;
    }

    /**
     * createCart 
     * 
     * @return Mage_Sales_Model_Quote
     */
    public function createCart()
    {
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($this->getStoreId());

        $quote = $this->addProductsToCart();

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
        return $quote;
    }

    /**
     * getStoreId
     * 
     * @return int
     */
    protected function getStoreId()
    {
        $data = $this->getCartData();
        $neighborhood = Mage::helper('apdc_neighborhood')->getNeighborhoodByPostcode($data['postcode']);
        $website = Mage::app()->getWebsite($neighborhood->getWebsiteId());
        return $website->getDefaultStore()->getStoreId();
    }

    protected function addProductsToCart()
    {
        $data = $this->getCartData();
        $partner = $this->getPartner();
        $quote = $this->initQuote();

        foreach ($data['products'] as $productId => $qty) {
            $product = Mage::getModel('catalog/product')->load($productId);
            if ($product && $product->getId()) {
                $params = [
                    'qty' => $qty
                ];
                if ($product->getHasOptions()) {
                    foreach ($product->getOptions() as $option) {
                        if ($option->getType() == 'drop_down') {
                            foreach ($option->getValues() as $optionValue) {
                                $params['options'] = [
                                    $option->getId() => $optionValue->getId()
                                ];
                                break;
                            }
                        }
                    }
                }
                $requestInfo = new Varien_Object($params);
                $quote->addProduct($product, $requestInfo);
            }
        }
        foreach ($quote->getAllItems() as $item) {
            $item->setApdcPartnerId($partner->getId());
        }
        $quote->setApdcPartnerRequest(serialize($data));
        $quote->setApdcPartnerId($partner->getId());
        $quote->getShippingAddress();
        $quote->getBillingAddress();
        $quote->collectTotals();
        $quote->save();
        return $quote;
    }

    protected function initQuote()
    {
        $quote = Mage::getModel('sales/quote');
        $quote->setStoreId($this->getStoreId());
        $quote->setCurrency(Mage::app()->getStore()->getBaseCurrencyCode());
        return $quote;
    }

    /**
     * loadQuote 
     * 
     * @param int $quoteId quoteId 
     * 
     * @return void
     */
    public function loadQuote($quoteId)
    {
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        $session = Mage::getSingleton('checkout/session');
        $session->replaceQuote($quote);
        Mage::getSingleton('customer/session')->setCartWasUpdated(true); 
        $quote->collectTotals()->save();
    }
}
