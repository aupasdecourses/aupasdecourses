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
 * Apdc_Catalog_Block_Adminhtml_Catalog_Categories_Toggle 
 * 
 * @category Apdc
 * @package  Catalog
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Form_Container
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Catalog_Block_Adminhtml_Catalog_Categories_Toggle extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_catalog';
        $this->_controller = 'adminhtml_catalog_categories';
        $this->_mode = 'toggle';
        $this->_headerText = $this->__('Toggle enable/disable');

        parent::__construct();

        $this->_removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('apdc_catalog')->__('Activer'));
        $this->_addButton(
            'disable',
            [
                'label'     => Mage::helper('adminhtml')->__('Désactiver'),
                'onclick'   => 'editForm.submit(\''.$this->getDisableCategoriesUrl().'\')',
                'class'     => 'save',
            ]
        );
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }

        return $this->getUrl('*/*/toggle', array('disable' => false));
    }

    /**
     * getDisableCategoriesUrl 
     * 
     * @return string
     */
    public function getDisableCategoriesUrl()
    {
        return $this->getUrl('*/*/toggle', array(
            'disable' => true
        ));
    }
}
