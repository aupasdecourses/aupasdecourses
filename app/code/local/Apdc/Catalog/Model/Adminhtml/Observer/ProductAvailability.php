<?php

class Apdc_Catalog_Model_Adminhtml_Observer_ProductAvailability
{
    /**
     * updateProductAvailability 
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function updateProductAvailability(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        Mage::getModel('apdc_catalog/product_availability_manager')->generateProductsAvailabilities([$product->getId()]);
    }

    /**
     * updateProductAvailabilityByShop 
     * 
     * @param Varien_Event_Observer $observer observer 
     * 
     * @return void
     */
    public function updateProductAvailabilityByShop(Varien_Event_Observer $observer)
    {
        $shop = $observer->getEvent()->getShop();
        if ($shop && $shop->getId()) {
            Mage::getModel('apdc_catalog/product_availability_manager')->generateProductsAvailabilitiesByShop($shop);
        }
    }

    /**
     * cronDailyUpdateProductAvailability 
     * 
     * @return void
     */
    public function cronDailyUpdateProductAvailability()
    {
        Mage::getModel('apdc_catalog/product_availability_manager')->generateProductsAvailabilities([]);
    }
}
