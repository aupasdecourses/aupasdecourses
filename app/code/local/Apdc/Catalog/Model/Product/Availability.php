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
 * Apdc_Catalog_Model_Product_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Product_Availability extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_catalog/product_availability');
    }

    /**
     * loadByIdDateWebsiteId 
     * 
     * @param int        $productId : productId 
     * @param string     $date      : date 
     * @param int | null $websiteId : websiteId 
     * 
     * @return Apdc_Catalog_Model_Product_Availability
     */
    public function loadByIdDateWebsiteId($productId, $date, $websiteId=null)
    {
        if (is_null($websiteId)) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }
        $collection = $this->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('delivery_date', $date)
            ->addFieldToFilter('website_id', $websiteId);

        return $collection->getFirstItem();
    }
}
