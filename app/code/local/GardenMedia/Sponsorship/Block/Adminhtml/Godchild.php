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
 * GardenMedia_Sponsorship_Block_Adminhtml_Godchild 
 * 
 * @category GardenMedia
 * @package  Sponsorship
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid_Container
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class GardenMedia_Sponsorship_Block_Adminhtml_Godchild extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gm_sponsorship';
        $this->_controller = 'adminhtml_godchild';
        $this->_headerText = Mage::helper('gm_sponsorship')->__('View godchilds');
        parent::__construct();


        $this->_removeButton('add');
    }
}
