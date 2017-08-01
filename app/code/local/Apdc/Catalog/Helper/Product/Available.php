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
 * Apdc_Catalog_Helper_Product_Available 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Helper_Product_Available extends Mage_Core_Helper_Abstract
{
    /**
     * isAvailable 
     * 
     * @param Mage_Catalog_Model_Product $product product 
     * 
     * @return boolean
     */
    public function isAvailable($product)
    {
        $available = true;
        $commercant = (int)$product->getData('commercant');
        if (!$commercant > 0 && in_array($product->getTypeId(), ['bundle', 'grouped'])) {
            $productCommercant = $this->getChildrenProductCommercantCollection($product);
            foreach ($productCommercant as $product) {
                if ((int)$product->getCommercant() > 0) {
                    $commercant = (int)$product->getCommercant();
                    break;
                }
            }
        }
        $shop = Mage::getModel('apdc_commercant/shop')
            ->getCollection()
            ->addFieldToFilter('id_attribut_commercant', $commercant)
            ->getFirstItem();
        $delivery_days = ($shop->getDeliveryDays() ? $shop->getDeliveryDays() : []);
        if(Mage::getSingleton('core/session')->getDdate()){
            $timestamp = strtotime(Mage::getSingleton('core/session')->getDdate());
            $day = date('w', $timestamp);
            if(!in_array($day, $delivery_days)){
                $available = false;
            }
        }
        return (boolean)$available;
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
}
