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
        if (!is_array($data['products'])) {
            $data['products'] = json_decode($data['products'], true);
        }
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

        $this->createPartnerProducts();
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

        foreach ($data['products'] as $prodData) {
            $product = Mage::getModel('catalog/product')->load($prodData['product_id']);
            if ($product && $product->getId()) {
                $params = [
                    'qty' => $prodData['qty']
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

    protected function createPartnerProducts()
    {
        $data = $this->getCartData();
        foreach ($data['products'] as $key => $prod) {
            if ($prod['type'] != 'apdc') {
                $prodData = $prod['product_data'];
                $dataModel = $this->getDataModel($prod['type']);
                $dataModel->setData($prodData);
                $dataModel->setPostcode($data['postcode']);
                $dataModel->checkSku();
                $product = Mage::getModel('catalog/product');
                $productId = $product->getIdBySku($dataModel->getSku());
                if ($productId) {
                    $storeId = Mage::app()->getStore()->getId();
                    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $product = $product->load($productId);
                    if ($product->getPrice() != $dataModel->getPrice()) {
                        $product->setPrice($dataModel->getPrice())
                            ->setPrixKiloSite($dataModel->getPrice() . '€/' . $dataModel->getUnitePrix())
                            ->setPrixPublic($dataModel->getPrice())
                            ->save();

                    }
                    Mage::app()->setCurrentStore($storeId);
                    $data['products'][$key]['product_id'] = $product->getId();
                    continue;
                }
                $product = $dataModel->getProduct();
                $product->save();
                Mage::getModel('apdc_catalog/product_availability_manager')->generateProductsAvailabilities([$product->getId()]);
                $data['products'][$key]['product_id'] = $product->getId();
            }
        }
        $this->setCartData($data);
    }

    /**
     * getDataModel
     * 
     * @param string $type type 
     * 
     * @return Apdc_Partner_Model_Partner_Abstract
     */
    protected function getDataModel($type)
    {
        return Mage::getSingleton('apdc_partner/partner_' . $type);
    }
}
