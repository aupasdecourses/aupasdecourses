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
 * Apdc_Catalog_Model_Resource_Product_Availability_Collection 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Model_Resource_Db_Collection_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Resource_Product_Availability_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * _construct 
     * 
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('apdc_catalog/product_availability');
    }
}
