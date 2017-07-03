<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * GardenMedia_Sponsorship_Block_Adminhtml_Godchild_Grid_Renderer_Action 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Adminhtml_Godchild_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
        $godchild = '<a href="' . Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getGodchildId())) . '" class="nobr">' . Mage::helper('gm_sponsorship')->__('Edit Godchild') . '</a>';
        $sponsor = '<a href="' . Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $row->getSponsorId())) . '" class="nobr">' . Mage::helper('gm_sponsorship')->__('Edit Sponsor') . '</a>';

        return $godchild . '<br />' . $sponsor;
    }
}
