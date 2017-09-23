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
 * Apdc_Neighborhood_Model_Source_Option_OpeningDays 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Model_Source_Option_OpeningDays
{
    public function getOptions()
    {
        return [
            1 => $this->getHelper()->__('Lundi'),
            2 => $this->getHelper()->__('Mardi'),
            3 => $this->getHelper()->__('Mercredi'),
            4 => $this->getHelper()->__('Jeudi'),
            5 => $this->getHelper()->__('Vendredi'),
            6 => $this->getHelper()->__('Samedi'),
            7 => $this->getHelper()->__('Dimanche'),
        ];
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getOptions() as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }
        return $options;
    }

    /**
     * getHelper 
     * 
     * @return Apdc_Neighborhood_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('apdc_neighborhood');
    }

}
