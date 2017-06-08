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
 * Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Grid_Renderer_Postcodes 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Grid_Renderer_Postcodes extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * render 
     * 
     * @param Varien_Object $row row 
     * 
     * @return void
     */
    public function render(Varien_Object $row) 
    {

        $postcodes = $row->getData($this->getColumn()->getIndex());
        if ($postcodes) {
            $postcodes = unserialize($postcodes);
            if (is_array($postcodes)) {
                return implode(', ', $postcodes);
            }
        }

        return '';
    }
}
