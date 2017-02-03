<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Block_Customer_Form_Register 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Customer_Block_Form_Register
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Customer_Form_Register extends Mage_Customer_Block_Form_Register
{

    /**
     * getSponsorCodeValue 
     * 
     * @return string
     */
    public function getSponsorCodeValue()
    {
        if ($this->getFormData()->getSponsorCode()) {
            return $this->getFormData()->getSponsorCode();
        }
        $sponsorData = Mage::getSingleton('core/session')->getSponsorData();
        if ($sponsorData && !empty($sponsorData)) {
            return $sponsorData->getSponsorCode()->getSponsorCode();
        }

        return '';
    }

    /**
     * getInstruction 
     * 
     * @return string
     */
    public function getInstruction()
    {
        return Mage::getStoreConfig('gm_sponsorship/general/register_instruction');
    }
}
