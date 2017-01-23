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
 * Apdc_Catalog_Block_Product_List_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_List_Availability extends Mage_Core_Block_Template
{

    protected $_availability;

    /**
     * getAvailability 
     *
     * @return array
     */
    public function getAvailability()
    {
        if(!isset($this->_availability)){
            $this->_availability = [];
            if (Mage::registry('current_category')) {
                $path = explode('/', Mage::registry('current_category')->getPath());
                if (isset($path[3])) {
                    $comcatid = $path[3];
                    $this->_availability= Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldtoFilter('id_category',$comcatid)->getFirstItem()->getDeliveryDays();
                }
            }
        }
        return  $this->_availability;
    }

    /**
     * getAvailability 
     * 
     * @param Mage_Catalog_Model_Product $product product 
     * 
     * @return array
     */
    public function getProductAvailability(Mage_Catalog_Model_Product $product)
    {
        if ($product->getCommercant()) {
            return Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldtoFilter('id_attribut_commercant',$product->getCommercant())->getFirstItem()->getDeliveryDays();
        }
        return [];
    }

    /**
     * getWeekDays 
     * 
     * @return array
     */
    public function getWeekDays()
    {
        //return Mage::getSingleton('adminhtml/system_config_source_locale_weekdays')->toOptionArray();
        return array(
            1 => array(
                'value' => 1,
                'label' => 'L'
            ),
            2 => array(
                'value' => 2,
                'label' => 'M'
            ),
            3 => array(
                'value' => 3,
                'label' => 'M'
            ),
            4 => array(
                'value' => 4,
                'label' => 'J'
            ),
            5 => array(
                'value' => 5,
                'label' => 'V'
            ),
            6 => array(
                'value' => 6,
                'label' => 'S'
            ),
            7 => array(
                'value' => 0,
                'label' => 'D'
            )
        );
    }
}
