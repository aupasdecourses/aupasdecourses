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
 * Apdc_Catalog_Block_Product_List_Card 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Catalog_Block_Product_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_List_Card extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * isAvailable 
     * 
     * @return array
     */
    public function getAvailability()
    {
        return Mage::helper('apdc_catalog/product_availability')->getAvailability($this->getProduct());
    }

    public function getSaisonnalite(){
        return Mage::helper('apdc_referentiel/product')->getSaisonnalite($this->getProduct());
    }

    public function getPoidsUnit(){
        return Mage::helper('apdc_referentiel/product')->getPoidsUnit($this->getProduct());
    }

    public function isCategoryPage(){
    	return Mage::registry('current_category');

    }
}
