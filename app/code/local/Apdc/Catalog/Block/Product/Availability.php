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
 * Apdc_Catalog_Block_Product_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Core_Block_Template
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Product_Availability extends Mage_Core_Block_Template
{

    protected $_availability = null;

    /**
     * getAvailability 
     * 
     * @return array | null
     */
    public function getAvailability()
    {
        if (!is_null($this->_availability)) {
            return $this->_availability;
        }
        $product = $this->getProduct();
        if($this->getRequest()->getControllerName()=='category'){
            return $this->getCategoryAvailability();
        } else if ($product && $product->getId()) {
            return $this->getProductAvailability($product);
        } else if (Mage::registry('current_product')) {
            return $this->getProductAvailability(Mage::registry('current_product'));
        }
        return [];
    }

    /**
     * setAvailability 
     * 
     * @param array $availability availability 
     * 
     * @return this
     */
    public function setAvailability($availability)
    {
        $this->_availability = $availability;
        return $this;
    }

    /**
     * getCategoryAvailability 
     *
     * @return array
     */
    public function getCategoryAvailability()
    {
        $availability = [];
        if (Mage::registry('current_category')) {
            $path = explode('/', Mage::registry('current_category')->getPath());
            if (isset($path[3])) {
                $comcatid = $path[3];

                $availability = Mage::getSingleton('apdc_commercant/shop')->getCollection()->addFieldtoFilter('id_category',array("finset"=>$comcatid))->getFirstItem()->getDeliveryDays();
            }
        }
        return  $availability;
    }

    /**
     * getProductAvailability 
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
     * @param boolean $short : display short days or not
     * 
     * @return array
     */
    public function getWeekDays($short=true)
    {
        return Mage::helper('apdc_commercant')->getWeekDays($short);
    }
}
