<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Commercant_Model_Source_Shop_Types 
 * 
 * @category Apdc
 * @package  Commercant
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Commercant_Model_Source_Shop_Types
{
    protected $options=null;

    public function toOptionArray()
    {
        if (is_null($this->options)) {
            $categories = Mage::getModel('catalog/category')->getCollection()
                ->addFieldToFilter('level', 2)
                ->addNameToResult();
            $categories->load();
            $options = [];
            foreach ($categories as $cat) {
                if (!isset($optins[$cat->getName()])) {
                    $options[$cat->getName()] = $cat->getName();
                }
            }
            asort($options);
            $this->options = $options;
        }
        return $this->options;
    }
}
