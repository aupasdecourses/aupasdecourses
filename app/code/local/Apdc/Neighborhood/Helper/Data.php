<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Neighborhood_Helper_Data 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Core_Helper_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * getNeighborhoodsByWebsiteId 
     * 
     * @param int          $websiteId : websiteId 
     * @param boolean|null $isActive  : get only active neighborhoods or not
     * 
     * @return Apdc_Neighborhood_Model_Resource_Neighborhood_Collection
     */
    public function getNeighborhoodsByWebsiteId($websiteId, $isActive=null)
    {
        $neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection()
            ->addFieldToFilter('website_id', $websiteId);
        if ($isActive === true) {
            $neighborhoods->addFieldToFilter('is_active', 1);
        }
        $neighborhoods->getSelect()->order('sort_order ASC');
        return $neighborhoods;
    }

    /**
     * getAllNeighborhoods 
     * 
     * @param boolean $isActive isActive 
     * 
     * @return Apdc_Neighborhood_Model_Resource_Neighborhood_Collection
     */
    public function getAllNeighborhoods($isActive=true)
    {
        $neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
        if ($isActive === true) {
            $neighborhoods->addFieldToFilter('is_active', 1);
        }
        $neighborhoods->getSelect()->order('sort_order ASC');
        return $neighborhoods;
    }

    /**
     * getNeighborhoodVisiting 
     * 
     * @return Apdc_Neighborhood_Model_Neighborhood
     */
    public function getNeighborhoodVisiting()
    {
        $neighborhood = Mage::getSingleton('customer/session')->getNeighborhoodVisiting();
        if (!$neighborhood) {
            $neighborhood = Mage::getModel('apdc_neighborhood/neighborhood');
        }
        return $neighborhood;
    }

    /**
     * getCustomerNeighborhood 
     * 
     * @return void
     */
    public function getCustomerNeighborhood()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer->getCustomerNeighborhood()) {
                return Mage::getModel('apdc_neighborhood/neighborhood')->load($customer->getCustomerNeighborhood())->getStoreUrl();
            } else {
                $store = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore();
                return $store->getBaseUrl();
            }
        }
        return $this->getVisitingNeighborhood()->getStoreUrl();
    }

    /**
     * getNeighborhoodByPostcode 
     * 
     * @param string       $postcode : postcode 
     * @param boolean|null $isActive : get only active neighborhoods or not
     * 
     * @return Apdc_Neighborhood_Model_Neighborhood | null
     */
    public function getNeighborhoodByPostcode($postcode, $isActive=null)
    {
        $neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
        if ($isActive === true) {
            $neighborhoods->addFieldToFilter('is_active', 1);
        }
        foreach ($neighborhoods as $neighborhood) {
            if (in_array($postcode, unserialize($neighborhood->getPostcodes()))) {
                return $neighborhood;
            }
        }
        return null;
    }

    public function UserName()
    {
        if (Mage::isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) {
            $name =  "Bonjour ".Mage::getSingleton('customer/session')->getCustomer()->getFirstname()."!";
        } else {
            $name = "Bonjour!";
        }

        return $name;
    }
}
