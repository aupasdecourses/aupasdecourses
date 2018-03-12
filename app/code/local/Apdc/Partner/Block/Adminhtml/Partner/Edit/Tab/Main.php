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
 * Apdc_Partner_Block_Adminhtml_Partner_Edit_Tab_Main 
 * 
 * @category Apdc
 * @package  Partner
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Partner_Block_Adminhtml_Partner_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_partner');
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('apdc_partner')->__('Informations sur le partenaire')));

        if ($model->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                array(
                    'name' => 'entity_id',
                    'value' => $model->getId()
                )
            );
        }
        $fieldset->addField(
            'is_active',
            'select',
            array(
                'name' => 'is_active',
                'label' => $this->_helper()->__('Is Active'),
                'title' => $this->_helper()->__('Is Active'),
                'values' => Mage::getSingleton('adminhtml/system_config_source_enabledisable')->toOptionArray(),
                'required' => true,
                'disabled' => $isElementDisabled
            )
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'name' => 'name',
                'label' => $this->_helper()->__('Nom du partenaire'),
                'title' => $this->_helper()->__('Nom du partenaire'),
                'required' => true,
                'disabled' => $isElementDisabled
            )
        );
        $fieldset->addField(
            'email',
            'text',
            array(
                'name' => 'email',
                'label' => $this->_helper()->__('Email du partenaire'),
                'title' => $this->_helper()->__('Email du partenaire'),
                'required' => true,
                'disabled' => $isElementDisabled
            )
        );
        $fieldset->addField(
            'partner_key',
            'text',
            array(
                'name' => 'partner_key',
                'label' => $this->_helper()->__('Clé du partenaire'),
                'title' => $this->_helper()->__('Clé du partenaire'),
                'required' => true,
                'disabled' => true
            )
        );
        $fieldset->addField(
            'partner_secret',
            'text',
            array(
                'name' => 'partner_secret',
                'label' => $this->_helper()->__('Secret du partenaire'),
                'title' => $this->_helper()->__('Secret du partenaire'),
                'required' => true,
                'disabled' => true
            )
        );



        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * getTableLabel
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('Informations sur le partenaire');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Informations sur le partenaire');
    }

    /**
     * canShowTab
     * 
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * isHidden
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * _helper 
     * 
     * @return Apdc_Partner_Helper_Data
     */
    private function _helper()
    {
        return Mage::helper('apdc_partner');
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
        return Mage::getSingleton('admin/session')->isAllowed('partners/apdc_partner/' . $action);
    }
}
