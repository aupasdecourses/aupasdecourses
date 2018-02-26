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
 * Apdc_Partner_Model_Resource_Partner_Collection 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Collection_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Model_Resource_Partner_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_partner/partner');
    }
}
