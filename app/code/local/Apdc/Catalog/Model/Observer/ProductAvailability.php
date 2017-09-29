<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Catalog_Model_Observer_ProductAvailability 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Observer_ProductAvailability
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
}
