<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Neighborhood_Model_Source_Option_Neighborhood 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Eav_Model_Entity_Attribute_Source_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Model_Source_Option_Neighborhood extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $isActive = null;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array();
            $neighborhoods = Mage::getModel('apdc_neighborhood/neighborhood')->getCollection();
            if ($this->isActive === true) {
                $neighborhoods->addFieldToFilter('is_active', 1);
            }
            $neighborhoods->getSelect()->order('sort_order ASC');
            if ($neighborhoods->count() > 0) {
                foreach ($neighborhoods as $neighborhood) {
                    $this->_options[] = array(
                        'label' => $neighborhood->getName(),
                        'value' => $neighborhood->getId()
                    );
                }
            }
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    /**
     * getOptionArray 
     * 
     * @param boolean| $isActive : if true will get all active options only
     * 
     * @return void
     */
    public function getOptionArray($isActive = null)
    {
        if ($isActive === true) {
            $this->isActive = true;
        } else {
            $this->isActive = null;
        }
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
    }
}
