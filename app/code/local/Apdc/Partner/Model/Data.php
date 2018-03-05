<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Model_Data 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Data extends Mage_Core_Model_Abstract
{
    /**
     * setPartner 
     * 
     * @param Apdc_Partner_Model_Partner $partner partner 
     * 
     * @return this
     */
    public function setPartner($partner)
    {
        $this->setData('partner', $partner);
        return $this;
    }

    /**
     * getPartner 
     * 
     * @return Apdc_Partner_Model_Partner
     */
    public function getPartner()
    {
        if (!$this->hasData('partner')) {
            throw Exception('You must set a partner');
        }
        return $this->getData('partner');
    }
}
