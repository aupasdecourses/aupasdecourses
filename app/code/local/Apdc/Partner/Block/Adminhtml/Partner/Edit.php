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
 * Apdc_Partner_Block_Adminhtml_Partner_Edit 
 * 
 * @category Apdc
 * @package  Partner
 * @uses     Mage
 * @uses     Mage_Adminhtml_Block_Widget_Form_Container
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Block_Adminhtml_Partner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'apdc_partner';
        $this->_controller = 'adminhtml_partner';

        parent::__construct();
        if (!$this->_isAllowedAction('save')) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        } else {
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('apdc_partner')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 100);

            $this->_formScripts[] = "function saveAndContinueEdit()" .
            "{editForm.submit($('edit_form').action + 'back/edit/')}";
        }

        if (!$this->_isAllowedAction('delete')) {
            $this->_removeButton('delete');
        }
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_partner')->getId()) {
            return Mage::helper('apdc_partner')->__('Modifier le partenaire');
        } else {
            return Mage::helper('apdc_partner')->__('Nouveau partenaire');
        }
    }

    protected function _isAllowedAction($action)
    {
        if ('index' == $action) {
            $action = null;
        } else {
            if ('new' == $action || 'save' == $action) {
                $action = 'edit';
            }
            $action = '/' . $action;
        }
        return Mage::getSingleton('admin/session')->isAllowed('system/apdc_partner' . $action);
    }
}
