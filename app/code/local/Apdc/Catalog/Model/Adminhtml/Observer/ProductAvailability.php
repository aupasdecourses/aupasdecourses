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
            if ($this->shopHasChanged($shop)) {
                Mage::getModel('apdc_catalog/product_availability_manager')->generateProductsAvailabilitiesByShop($shop);
            }
        }
    }

    /**
     * shopHasChanged 
     * 
     * @param Apdc_Commercant_Model_Shop $shop shop 
     * 
     * @return boolean
     */
    protected function shopHasChanged(Apdc_Commercant_Model_Shop $shop)
    {
        $origData = $shop->getOrigData();
        if ($origData['delivery_days'] !== $shop->getDeliveryDays()) {
            return true;
        }
        $nbClosingPeriodsOrigData = count($origData['closing_periods']);
        $nbClosingPeriods = count($shop->getClosingPeriods());
        if ($nbClosingPeriods != $nbClosingPeriodsOrigData) {
            return true;
        } else if ($nbClosingPeriodsOrigData > 0) {
            foreach ($shop->getClosingPeriods() as $key => $closingPeriods) {
                if ($closingPeriods !== $origData['closing_periods'][$key]) {
                    return true;
                }
            }
        }
        return false;
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
