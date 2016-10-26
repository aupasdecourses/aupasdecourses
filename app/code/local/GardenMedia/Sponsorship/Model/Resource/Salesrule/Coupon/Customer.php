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
 * GardenMedia_Sponsorship_Model_Resource_Salesrule_Coupon_Customer 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Model_Resource_Salesrule_Coupon_Customer extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        $this->_init('gm_sponsorship/salesrule_coupon_customer', 'entity_id');
    }
}
