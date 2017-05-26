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
    protected $visitingNeighborhood = null;

    /**
     * getNeighborhoods 
     * get all active Neighborhoods
     * 
     * @return Apdc_Neighborhood_Model_Resource_Neighborhood_Collection
     */
    public function getNeighborhoods()
    {
        $collection = Mage::getModel('apdc_neighborhood/neighborhood')
            ->getCollection()
            ->addFieldToFilter('is_active', 1);
        $collection->getSelect()->order('sort_order ASC');

        return $collection;
    }

    /**
     * getCurrentNeighborhood 
     * 
     * @return Apdc_Neighborhood_Model_NeighborHood | null
     */
    public function getCurrentNeighborhood()
    {
        if ($this->getCustomer()) {
            if ($this->getCustomer()->getCustomerNeighborhood()) {
                return Mage::getModel('apdc_neighborhood/neighborhood')->load($this->getCustomer()->getCustomerNeighborhood());
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
     * getInformationsType 
     * 
     * @return string | null
     */
    public function getInformationsType()
    {
        $currentNeighborhood = $this->getCurrentNeighborhood();
        $currentWebsiteId = Mage::app()->getWebsite()->getId();
        if ($this->getCustomer() && !$currentNeighborhood) {
            if ($this->getSession()->getNeighborhoodIUnderstood() === true) {
                return 'no_neighborhood';
            } else {
                return 'new_neighborhood';
            }
        } else if (
            $this->getCustomer() && $currentNeighborhood &&
            $currentNeighborhood->getWebsiteId() != $currentWebsiteId
        ) {
            return 'not_same_website';
        }

        return null;
    }

    /**
     * getSaveUrl 
     * 
     * @param Apdc_Neighborhood_Model_NeighborHood $neighborhood neighborhood 
     * 
     * @return string
     */
    public function getSaveUrl(Apdc_Neighborhood_Model_NeighborHood $neighborhood)
    {
        return $this->getUrl('apdc_neighborhood/index/save', array('id' => $neighborhood->getId()));
    }

    /**
     * getVisitUrl 
     * 
     * @param Apdc_Neighborhood_Model_NeighborHood $neighborhood neighborhood 
     * 
     * @return string
     */
    public function getVisitUrl(Apdc_Neighborhood_Model_NeighborHood $neighborhood)
    {
        return $this->getUrl('apdc_neighborhood/index/visit', array('id' => $neighborhood->getId()));
    }

    /**
     * getSaveVisitUrl 
     * 
     * @return string | null
     */
    public function getSaveVisitUrl()
    {
        if ($this->getSession()->getNeighborhoodVisitingId()) {
            $neighborhood = $this->getVisitingNeighborhood();
            return $this->getSaveUrl($neighborhood);
        }
        return null;
    }

    /**
     * getVisitingNeighborhood 
     * 
     * @return Apdc_Neighborhood_Model_NeighborHood
     */
    public function getVisitingNeighborhood()
    {
        if (is_null($this->visitingNeighborhood)) {
            $neighborhood = Mage::getModel('apdc_neighborhood/neighborhood');
            if ($this->getSession()->getNeighborhoodVisitingId()) {
                $neighborhood = $neighborhood->load((int)$this->getSession()->getNeighborhoodVisitingId());
            }
            $this->visitingNeighborhood = $neighborhood;
        }
        return $this->visitingNeighborhood;
    }

    /**
     * getNeighborhoodIUnderstoodUrl 
     * 
     * @return string
     */
    public function getNeighborhoodIUnderstoodUrl()
    {
        return $this->getUrl('apdc_neighborhood/index/ajaxIUnderstood');
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
}
