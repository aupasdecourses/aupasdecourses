<?php

/**
 * This file is part of the GardenMedia Mission Project 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
/**
 * Apdc_Partner_Block_Adminhtml_Partner 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Grid_Container
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Block_Adminhtml_Partner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_partner';
        $this->_controller = 'adminhtml_partner';
        $this->_headerText = Mage::helper('apdc_partner')->__('Gestion des partenaires');
        parent::__construct();


        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('add', 'label', Mage::helper('apdc_partner')->__('Ajouter un partenaire'));
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
        return Mage::getSingleton('admin/session')->isAllowed('system/apdc_partner/' . $action);
    }
}
