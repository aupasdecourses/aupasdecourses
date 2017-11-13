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
 * Apdc_Catalog_Model_Source_Product_Days 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Eav_Model_Entity_Attribute_Source_Table
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Model_Source_Product_Days extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    protected $flatOptions = null;

    public function getOptions()
    {
        if (is_null($this->flatOptions)) {
            $this->flatOptions = [];
            foreach ($this->getAllOptions() as $option) {
                $this->flatOptions[$option['value']] = $option['label'];
            }
        }
        return $this->flatOptions;
    }

    /**
     * getAllOptions
     * 
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'value' => 1,
                    'label' => $this->_helper()->__('Lundi')
                ],
                [
                    'value' => 2,
                    'label' => $this->_helper()->__('Mardi')
                ],
                [
                    'value' => 3,
                    'label' => $this->_helper()->__('Mercredi')
                ],
                [
                    'value' => 4,
                    'label' => $this->_helper()->__('Jeudi')
                ],
                [
                    'value' => 5,
                    'label' => $this->_helper()->__('Vendredi')
                ],
                [
                    'value' => 6,
                    'label' => $this->_helper()->__('Samedi')
                ],
                [
                    'value' => 7,
                    'label' => $this->_helper()->__('Dimanche')
                ]
            ];
        }
        return $this->_options;
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

        return '';
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
