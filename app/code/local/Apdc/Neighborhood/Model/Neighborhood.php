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
 * Apdc_Neighborhood_Model_Neighborhood 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Model_Neighborhood extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_neighborhood/neighborhood');
    }

    /**
     * getStoreUrl 
     * 
     * @return string
     */
    public function getStoreUrl()
    {
        return Mage::app()->getWebsite($this->getWebsiteId())->getDefaultStore()->getUrl();
    }

    public function isOpen()
    {
        $currentDay = date('N');
        if (in_array($currentDay, $this->getOpeningDays())) {
            return true;
        }
        return false;
    }
}
