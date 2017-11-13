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
 * Apdc_Catalog_Model_Source_Product_Availability 
 * 
 * @category Apdc
 * @package  Catalog
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Source_Product_Availability
{
    protected $options = null;

    /**
     * getOptions
     * 
     * @return array
     */
    public function getOptions()
    {
        if (is_null($this->options)) {
            $this->options = [
                0 => $this->_helper()->__('Produit non disponible'),
                1 => $this->_helper()->__('Produit disponible'),
                2 => $this->_helper()->__('Le service n\'est pas disponible ce jour'),
                3 => $this->_helper()->__('Le commerçant ne peut assurer la livraison ce jour'),
                4 => $this->_helper()->__('Le commerçant est en congé'),
                5 => $this->_helper()->__('Le produit n\'est pas dispobible ce jour')
            ];
        }
        return $this->options;
    }

    /**
     * getOptionLabel
     * 
     * @param int $optionId optionId 
     * 
     * @return string
     */
    public function getOptionLabel($optionId)
    {
        $options = $this->getOptions();
        if (isset($options[$optionId])) {
            return $options[$optionId];
        }

        return $options[0];
    }

    /**
     * _helper
     * 
     * @return Apdc_Catalog_Helper_Data
     */
    private function _helper()
    {
        return Mage::helper('apdc_catalog');
    }
}
