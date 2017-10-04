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
 * Apdc_Catalog_Helper_Product_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Helper_Product_Availability extends Mage_Core_Helper_Abstract
{
    /**
     * productAvailability 
     * 
     * @var array
     */
    protected $productAvailability = [];

    /**
     * getAvailability 
     * 
     * @param Mage_Catalog_Model_Product $product product 
     * 
     * @return array
     */
    public function getAvailability(Mage_Catalog_Model_Product $product)
    {
        if (!isset($this->productAvailability[$product->getId()])) {
            $availablilityId = 1;
            if ($product->getProductAvailability()) {
                $availablilityId = $product->getProductAvailability();
            } else  if (Mage::getSingleton('core/session')->getDdate()) {
                $timestamp = strtotime(Mage::getSingleton('core/session')->getDdate());
                $date = date('Y-m-d', $timestamp);
                $availablility = Mage::getModel('apdc_catalog/product_availability')->loadByIdDateWebsiteId($product->getId(), $date);
                $availablilityId = 0;
                if ($availablility && $availablility->getId()) {
                    $availablilityId = $availablility->getStatus();
                }
            }
            switch ($availablilityId) {
            case 1:
                $isAvailable = true;
                break;
            default:
                $isAvailable = false;
            }
            $this->productAvailability[$product->getId()] = [
                'is_available' => $isAvailable,
                'message' => $this->getAvailabilityMessage($availablilityId),
                'product_availability' => $availablilityId
            ];
        }
        return $this->productAvailability[$product->getId()];
    }

    /**
     * getChildrenProductCommercantCollection 
     *
     * @param Mage_Catalog_Model_Product $product : magento product object
     * 
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function getChildrenProductCommercantCollection($product)
    {
        $childrenIds = $product->getTypeInstance()
            ->getChildrenIds($product->getId(), true);

        $products = Mage::getModel('catalog/product')->getCollection()
            ->addFieldToFilter('entity_id', ['in' => $childrenIds]);
        $products->addAttributeToSelect('commercant');
        $products->load();

        return $products;
    }

    /**
     * getAvailabilityMessage 
     * 
     * @param int $availablilityId availablilityId 
     * 
     * @return string
     */
    protected function getAvailabilityMessage($availablilityId)
    {
        return Mage::getSingleton('apdc_catalog/source_product_availability')->getOptionLabel($availablilityId);
    }
}
