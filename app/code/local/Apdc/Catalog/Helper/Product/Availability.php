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
             $productAvailability = [
                'can_order' => $product->getCanOrder(),
                'can_order_days' => (!is_null($product->getCanOrderDays()) ? $product->getCanOrderDays() : '1,2,3,4,5,6,7'),
                'is_available' => $isAvailable,
                'is_available_for_sale' => ($isAvailable && $product->getCanOrder()),
                'product_availability' => $availablilityId
            ];
            $productAvailability['message'] = $this->getAvailabilityMessage($productAvailability);
            $this->productAvailability[$product->getId()] = $productAvailability;
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
     * @param array $productAvailability : product availablility from Apdc_Catalog_Helper_Product_Availability getAvailability
     * 
     * @return string
     */
    protected function getAvailabilityMessage($productAvailability)
    {
        if (!$productAvailability['can_order']) {
            return Mage::helper('apdc_catalog')->__('Produit non disponible à la commande aujourd\'hui');
        }
        return Mage::getSingleton('apdc_catalog/source_product_availability')->getOptionLabel($productAvailability['product_availability']);
    }
}
