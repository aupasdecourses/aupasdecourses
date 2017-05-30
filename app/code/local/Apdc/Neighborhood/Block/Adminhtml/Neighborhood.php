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
 * Apdc_Neighborhood_Block_Adminhtml_Neighborhood 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid_Container
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Block_Adminhtml_Neighborhood extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_neighborhood';
        $this->_controller = 'adminhtml_neighborhood';
        $this->_headerText = Mage::helper('apdc_neighborhood')->__('Gestion des quartiers');
        parent::__construct();


        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('add', 'label', Mage::helper('apdc_neighborhood')->__('Ajouter un quartier'));
        } else {
            $this->_removeButton('add');
        }
    }

    /**
     * _isAllowedAction
     * 
     * @param string $action : action
     * 
     * @return boolean
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('neighborhoods/apdc_neighborhood/' . $action);
    }
}
