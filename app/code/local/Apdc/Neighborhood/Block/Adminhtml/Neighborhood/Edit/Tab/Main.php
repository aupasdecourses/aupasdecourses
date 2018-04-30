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
 * Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Edit_Tab_Main 
 * 
 * @category Apdc
 * @package  Neighborhood
 * @author   Erwan INYZANT <erwan@garden-media.fr> 
 * @license  All right reserved to Garden Media Studio VN Company Limited
 * @link     http://www.garden-media.fr
 */
class Apdc_Neighborhood_Block_Adminhtml_Neighborhood_Edit_Tab_Main
    extends Mage_Adminhtml_Block_Widget_Form 
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_neighborhood');
        $postcodes = $model->getPostcodes();
        if (is_array($postcodes)) {
            $model->setPostcodes(implode(',', $postcodes));
        }
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => Mage::helper('apdc_neighborhood')->__('Informations sur le quartier')));

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
            'website_id',
            'select',
            array(
                'name' => 'website_id',
                'label' => $this->_helper()->__('Website'),
                'title' => $this->_helper()->__('Website'),
                'values' => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
                'required' => true,
                'disabled' => $isElementDisabled
            )
        );

        $fieldset->addField(
            'code_do',
            'text',
            array(
                'name' => 'code_do',
                'label' => $this->_helper()->__('Code Do'),
                'title' => $this->_helper()->__('Code Do'),
                'required' => true,
                'disabled' => $isElementDisabled
            )
        );

        $fieldset->addField(
            'mistral_guid',
            'text',
            array(
                'name' => 'mistral_guid',
                'label' => $this->_helper()->__('Mistral GUID'),
                'title' => $this->_helper()->__('Mistral GUID'),
                'required' => false,
                'disabled' => $isElementDisabled
            )
        );

        
        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFormData());
        } else {
            $form->setValues($model->getData());
        }
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
        return $this->_helper()->__('Informations sur le quartier');
    }

    /**
     * getTabTitle
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('Informations sur le quartier');
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
     * @return Apdc_Neighborhood_Helper_Data
     */
    private function _helper()
    {
        return Mage::helper('apdc_neighborhood');
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
