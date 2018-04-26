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
 * Apdc_Neighborhood_Block_Header_Menu 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Block_Header_Menu extends Mage_Core_Block_Template
{
    /**
     * getLastCustomerNeighborhood
     * 
     * @return Mage_Core_Model_Store | null
     */
    public function getLastCustomerNeighborhood()
    {
        if ($this->getCustomer()) {
            if ($this->getCustomer()->getCustomerNeighborhood()) {
                return Mage::app()->getStore($this->getCustomer()->getCustomerNeighborhood());
            }
        }
        return null;
    }

    /**
     * getCustomer 
     * 
     * @return Mage_Customer_Model_Customer | null
     */
    public function getCustomer()
    {
        if ($this->getSession()->isLoggedIn()) {
            return $this->getSession()->getCustomer();
        }
        return null;
    }

    /**
     * getSaveUrl 
     * 
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('apdc_neighborhood/index/save');
    }

    /**
     * getSession 
     * 
     * @return Mage_Customer_Model_session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * getCurrentNeighborhood 
     * 
     * @return Mage_Core_Model_Store
     */
    public function getCurrentNeighborhood()
    {
        return Mage::helper('apdc_neighborhood')->getCurrentNeighborhood();
    }
}
